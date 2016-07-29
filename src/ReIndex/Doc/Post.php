<?php

/**
 * @file Post.php
 * @brief This file contains the Post class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use ReIndex\Property;
use ReIndex\Helper;
use ReIndex\Collection;
use ReIndex\Exception;
use ReIndex\Security\Role;
use ReIndex\Enum\State;
use ReIndex\Task\IndexPostTask;
use ReIndex\Security\User\System;

use Phalcon\Di;


/**
 * @brief This class is used to represent a generic entry, a content created by a user.
 * @details Every post is versioned into the database, has tags and also a owner, who created the entry.
 * @nosubgrouping
 *
 * @cond HIDDEN_SYMBOLS
 *
 * @property int $legacyId        // Legacy identifier, in case you import from an old password.
 *
 * @property string $title        // Title.
 * @property string $slug         // A short name given to an article that is in production.
 *
 * @property int $publishedAt     // Publishing timestamp.
 *
 * @property string $protection   // [readonly] Level of protection.
 * @property string $protectorId  // [readonly] The user ID of whom protected the content.
 *
 * @property Collection\TagCollection $tags // A collection of tags.
 * @property Collection\TaskCollection $tasks // A collection of tasks.
 * @property Collection\SubscriptionCollection $subscriptions // A collection of members who have subscribed the post.
 *
 * @endcond
 */
abstract class Post extends Versionable {
  use Property\TExcerpt, Property\TBody, Property\TDescription;

  /** @name Constants */
  //!@{

  const HASH = '_pt'; //!< Post's Redis hash postfix.

  const NEW_SET = 'new_'; //!< Newest posts Redis set.
  const POP_SET = 'pop_'; //!< Popular posts Redis set.
  const ACT_SET = 'act_'; //!< Active posts Redis set.
  const OPN_SET = 'opn_'; //!< Open questions Redis set.

  const POP_TAGS_SET = 'pop_tags_'; //!< Popular tags Redis set.
  const ACT_TAGS_SET = 'act_tags_'; //!< Active tags Redis set.

  const CLOSED_PL = 'closed'; //!< The post is closed.
  const LOCKED_PL = 'locked'; //!< The post is locked.

  //!@}

  private $tags;          // Collection of tags.
  private $tasks;         // Collection of tasks.
  private $subscriptions; // A collection of members who have subscribed the post.


  public function __construct() {
    parent::__construct();

    $this->tags = new Collection\TagCollection('tags', $this->meta);
    $this->tasks = new Collection\TaskCollection('tasks', $this->meta);
    $this->subscriptions = new Collection\SubscriptionCollection($this);

    $this->votes->onCastVote = 'zRegisterVote';

    // Since we can't use reflection inside EoC Server, we need a way to recognize every subclass of the `Post` class.
    // This is done testing `isset($doc->supertype) && $doc->supertype == 'post'`.
    $this->meta['supertype'] = 'post';
  }


  /**
   * @brief Calls zIncrBy() many times to update different sets.
   * @param[in] string $set The name of the base Redis set.
   * @param[in] \DateTime $date A date.
   * @param[in] int $value A integer value.
   */
  private function zMultipleIncrBy($set, \DateTime $date, $value) {
    $id = $this->unversionId;

    $this->redis->zIncrBy($set, $value, $id);
    $this->redis->zIncrBy($set.$date->format('_Ymd'), $value, $id);
    $this->redis->zIncrBy($set.$date->format('_Ym'), $value, $id);
    $this->redis->zIncrBy($set.$date->format('_Y'), $value, $id);
    $this->redis->zIncrBy($set.$date->format('_Y_w'), $value, $id);
  }


  /**
   * @brief Registers the vote into Redis database.
   * @warning Don't call this function unless you know what are you doing.
   * @param[in] int $value The vote.
   */
  public function zRegisterVote($value) {
    $date = (new \DateTime())->setTimestamp($this->publishedAt);

    // Marks the start of a transaction block. Subsequent commands will be queued for atomic execution using `exec()`.
    $this->redis->multi();

    $this->zMultipleIncrBy(self::POP_SET.'post', $date, $value);
    $this->zMultipleIncrBy(self::POP_SET.$this->type, $date, $value);

    $uniqueMasters = $this->tags->uniqueMasters();
    foreach ($uniqueMasters as $tagId) {
      $prefix = self::POP_SET . $tagId . '_';
      $this->zMultipleIncrBy($prefix.'post', $date, $value);
      $this->zMultipleIncrBy($prefix.$this->type, $date, $value);
    }

    // Marks the end of the transaction block.
    $this->redis->exec();
  }


  /**
   * @brief Given a list of IDs, returns the correspondent objects.
   * @retval array
   */
  public static function collect(array $ids) {
    if (empty($ids))
      return [];

    $di = Di::getDefault();
    $couch = $di['couchdb'];
    $redis = $di['redis'];
    $user = $di['guardian']->getUser();

    $opts = new ViewQueryOpts();

    // Posts.
    $opts->doNotReduce();
    $posts = $couch->queryView("posts", "all", $ids, $opts);

    Helper\ArrayHelper::unversion($ids);

    // Likes.
    if ($user->isMember()) {
      $opts->reset();
      $opts->doNotReduce()->includeMissingKeys();

      $keys = [];
      foreach ($ids as $postId)
        $keys[] = [$postId, $user->id];

      $likes = $couch->queryView("votes", "perItemAndMember", $keys, $opts);
    }
    else
      $likes = [];

    // Scores.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $scores = $couch->queryView("votes", "perItem", $ids, $opts);

    // Replies.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $replies = $couch->queryView("replies", "perPost", $ids, $opts);

    // Members.
    $creatorIds = array_column(array_column($posts->asArray(), 'value'), 'creatorId');
    $opts->reset();
    $opts->doNotReduce()->includeMissingKeys();
    $members = $couch->queryView("members", "allNames", $creatorIds, $opts);

    $entries = [];
    $postCount = count($posts);
    for ($i = 0; $i < $postCount; $i++) {
      $entry = (object)($posts[$i]['value']);
      $entry->id = $posts[$i]['id'];

      if ($entry->state == State::CURRENT) {
        $entry->url = Helper\Url::build($entry->publishedAt, $entry->slug);
        $entry->timestamp = Helper\Time::when($entry->publishedAt);
      }
      else {
        $entry->url = Helper\Url::build($entry->createdAt, $entry->slug);
        $entry->timestamp = Helper\Time::when($entry->createdAt);
      }

      $entry->username = $members[$i]['value'][0];
      $entry->gravatar = Member::getGravatar($members[$i]['value'][1]);
      $entry->hitsCount = Helper\Text::formatNumber($redis->hGet(Helper\Text::unversion($entry->id), 'hits'));
      $entry->score = is_null($scores[$i]['value']) ? 0 : $scores[$i]['value'];
      $entry->repliesCount = is_null($replies[$i]['value']) ? 0 : $replies[$i]['value'];
      $entry->liked = $user->isGuest() || is_null($likes[$i]['value']) ? FALSE : TRUE;

      if (!empty($entry->tags)) {
        // Tags.
        $opts->reset();
        $opts->doNotReduce();

        // Resolves the synonyms.
        $synonyms = $couch->queryView("tags", "synonyms", array_keys($entry->tags), $opts);

        // Extracts the masters.
        $masters = array_unique(array_column($synonyms->asArray(), 'value'));

        $entry->tags = $couch->queryView("tags", "allNames", $masters, $opts);
      }
      else
        $entry->tags = [];

      $entries[] = $entry;
    }

    return $entries;
  }


  /**
   * @brief Likes the post.
   */
  public function like() {
    return $this->votes->cast(1);
  }


  /** @name Protection Methods */
  //!@{

  /**
   * @brief Returns `true` if the post has some kind of protection, `false` otherwise.
   * @retval bool
   */
  public function isProtected() {
    return $this->isMetadataPresent('protection');
  }


  /**
   * @brief Returns the protection if any.
   * @retval string
   */
  public function getProtection() {
    return ($this->isProtected()) ? $this->meta['protection'] : NULL;
  }


  /**
   * @brief Returns the id of the user who protected the post.
   * @retval string
   */
  public function getProtectorId() {
    return ($this->isProtected()) ? $this->meta['protectorId'] : NULL;
  }


  /**
   * @brief Closes the post.
   * @details No more answers or comments can be added.
   * @see http://meta.stackexchange.com/questions/10582/what-is-a-closed-or-on-hold-question
   */
  public function close() {
    if (!$this->user->has(new Role\ModeratorRole\ProtectPostPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    $this->meta['protection'] = self::CLOSED_PL;
    $this->meta['protectorId'] = $this->user->id;
  }


  /**
   * @brief Locks the post.
   * @details No more new answers (or comments), votes, edits, question comments.
   * @see http://meta.stackexchange.com/questions/22228/what-is-a-locked-post
   */
  public function lock() {
    if (!$this->user->has(new Role\ModeratorRole\ProtectPostPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    $this->meta['protection'] = self::LOCKED_PL;
    $this->meta['protectorId'] = $this->user->id;
  }


  /**
   * @brief Removes the post protection.
   */
  public function unprotect() {
    if (!$this->user->has(new Role\ModeratorRole\UnprotectPostPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    $this->unsetMetadata('protection');
    $this->unsetMetadata('protectorId');
  }


  /**
   * @brief Returns `true` if the post is closed.
   */
  public function isClosed() {
    return ($this->isProtected() && $this->protection === self::CLOSED_PL) ? TRUE : FALSE;
  }

  /**
   * @brief Returns `true` if the post is locked.
   */
  public function isLocked() {
    return ($this->isProtected() && $this->protection === self::LOCKED_PL) ? TRUE : FALSE;
  }

  //!@}


  /**
   * @copydoc Versionable::submit()
   */
  public function submit () {
    parent::submit();

    if ($this->user->match($this->creatorId)) {
      $this->state->set(State::INDEXING);
      $this->tasks->add(new IndexPostTask($this));
    }
    else
      $this->state->set(State::SUBMITTED);

    // Finally saves the document itself.
    $this->save();
  }


  /**
   * @copydoc Versionable::approve()
   */
  public function approve() {
    parent::approve();

    if ($this->user instanceof System || !$this->indexingInProgress() || $this->votes->count(FALSE) >= $this->di['config']->review->scoreToApproveRevision) {
      $this->state->set(State::INDEXING);
      $this->tasks->add(new IndexPostTask($this));
      $this->save();
    }
  }


  /**
   * @copydoc Versionable::delete()
   */
  public function delete() {
    parent::delete();
    $this->state->set(State::DELETING);
    $this->tasks->add(new IndexPostTask($this));
    $this->save();
  }


  /**
   * @copydoc Versionable::restore()
   */
  public function restore() {
    parent::restore();

    if ($this->state->is(State::INDEXING))
      $this->tasks->add(new IndexPostTask($this));

    $this->save();
  }


  /**
   * @brief Returns a measure of the time passed since the publishing date. In case is passed more than a day, returns
   * a human readable date.
   * @retval string
   */
  public function whenHasBeenPublished() {
    return Helper\Time::when($this->publishedAt);
  }


  /**
   * @brief Gets the resource permanent link.
   * @retval string
   */
  public function getPermalink() {
    return "/".$this->id;
  }


  /**
   * @brief Gets the post URL.
   * @retval string
   */
  public function getHref() {
    return Helper\Url::build($this->publishedAt, $this->getSlug());
  }


  /**
   * @brief Gets the timestamp of the post's last update.
   * @details The timestamp is updated when a reply (or an answer or a review) is inserted or modified and yet when a
   * comment is inserted or modified.
   * @retval int
   */
  public function getLastUpdate() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit(1);

    $rows = $this->couch->queryView("updates", "perDateByPostId", NULL, $opts);

    if ($rows->isEmpty())
      $lastUpdate = $this->modifiedAt;
    else
      $lastUpdate = $rows[0]['key'][0];

    return ($this->modifiedAt >= $lastUpdate) ? $this->modifiedAt : $lastUpdate;
  }


  /**
   * @brief Returns the original tags associated to the current post.
   * @return array
   */
  public function getOriginalTags() {
    return $this->originalTags;
  }


  /** @name Replaying Methods */
  //!@{

  /**
   * @brief Get the post replays, answers, in case of a question, else comments
   */
  public function getReplies() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setStartKey([$this->unversionId, Couch::WildCard()])->setEndKey([$this->unversionId])->includeDocs();
    $rows = $this->couch->queryView("replies", "newestPerPost", NULL, $opts);

    $replies = [];
    foreach ($rows as $row) {
      $reply = new Reply();
      $reply->assignArray($row['doc']);
      $replies[] = $reply;
    }

    return $replies;
  }


  /**
   * @brief Gets the number of the answer or comments.
   */
  public function getRepliesCount() {
    $opts = new ViewQueryOpts();
    $opts->groupResults();
    return $this->couch->queryView("replies", "perPost", [$this->unversionId], $opts)->getReducedValue();
  }

  //!@}


  //! @cond HIDDEN_SYMBOLS

  public function getLegacyId() {
    return $this->meta['legacyId'];
  }


  public function issetLegacyId() {
    return isset($this->meta['legacyId']);
  }


  public function setLegacyId($value) {
    $this->meta['legacyId'] = $value;
  }


  public function unsetLegacyId() {
    if ($this->isMetadataPresent('legacyId'))
      unset($this->meta['legacyId']);
  }


  public function getTitle() {
    return $this->meta['title'];
  }


  public function issetTitle() {
    return isset($this->meta['title']);
  }


  public function setTitle($value) {
    $this->meta['title'] = trim($value);
  }


  public function unsetTitle() {
    if ($this->isMetadataPresent('title'))
      unset($this->meta['title']);
  }


  public function getSlug() {
    return $this->meta['slug'];
  }

  
  public function issetSlug() {
    return isset($this->meta['slug']);
  }


  public function setSlug($value) {
    $this->meta['slug'] = trim($value);
  }


  public function unsetSlug() {
    if ($this->isMetadataPresent('slug'))
      unset($this->meta['slug']);
  }


  public function getPublishedAt() {
    return $this->meta['publishedAt'];
  }


  public function issetPublishedAt() {
    return isset($this->meta['publishedAt']);
  }


  public function setPublishedAt($value) {
    $this->meta['publishedAt'] = $value;

    // Used to group by year, month and day.
    $this->meta['year'] = date("Y", $value);
    $this->meta['month'] = date("m", $value);
    $this->meta['day'] = date("d", $value);
  }


  public function unsetPublishedAt() {
    if ($this->isMetadataPresent('publishedAt'))
      unset($this->meta['publishedAt']);
  }


  public function getTags() {
    return $this->tags;
  }


  public function issetTags() {
    return isset($this->tags);
  }


  public function getTasks() {
    return $this->tasks;
  }


  public function issetTasks() {
    return isset($this->tasks);
  }


  public function getSubscriptions() {
    return $this->subscriptions;
  }


  public function issetSubscriptions() {
    return isset($this->subscriptions);
  }

  //! @endcond

}