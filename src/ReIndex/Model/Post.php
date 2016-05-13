<?php

/**
 * @file Post.php
 * @brief This file contains the Post class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use ReIndex\Extension;
use ReIndex\Property;
use ReIndex\Helper;
use ReIndex\Enum;
use ReIndex\Exception;
use ReIndex\Security\Role;
use ReIndex\Enum\VersionState;

use Phalcon\Di;


/**
 * @brief This class is used to represent a generic entry, a content created by a user.
 * @details Every post is versioned into the database, has tags and also a owner, who created the entry.
 * @nosubgrouping
 */
abstract class Post extends Versionable implements Extension\ICache, Extension\ICount, Extension\IStar, Extension\IVote, Extension\ISubscribe {
  use Extension\TCount, Extension\TStar, Extension\TVote, Extension\TSubscribe;
  use Property\TExcerpt, Property\TBody, Property\TDescription;

  /** @name Redis Set Names */
  //!@{

  const NEW_SET = 'new_'; //!< Newest posts Redis set.
  const POP_SET = 'pop_'; //!< Popular posts Redis set.
  const ACT_SET = 'act_'; //!< Active posts Redis set.
  const OPN_SET = 'opn_'; //!< Open questions Redis set.

  //!@}

  /** @name Protection Levels */
  //!@{

  const CLOSED_PL = 'closed'; //!< The post is closed.
  const LOCKED_PL = 'locked'; //!< The post is locked.

  //!@}

  // Since the user can add new tags in a second moment, we must store in a member the original tags, otherwise the zRem
  // methods will not work properly.
  private $zRemTags;

  protected $markdown; // Stores the Markdown parser instance.
  protected $log; // Stores the logger instance.


  public function __construct() {
    parent::__construct();
    $this->markdown = $this->di['markdown'];
    $this->log = $this->di['log'];

    // After the creation the post must be visible.
    $this->meta['visible'] = TRUE;

    $this->zRemTags = ($this->isMetadataPresent('tags')) ? Helper\ArrayHelper::merge($this->meta['tags'], $this->uniqueMasters()->asArray()) : [];
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

      if ($entry->state == VersionState::CURRENT) {
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
        $synonyms = $couch->queryView("tags", "synonyms", $entry->tags, $opts);

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
   * @brief Saves the post.
   * @param[in] bool $deferred When `true` doesn't update the indexes.
   */
  public function save($deferred = FALSE) {
    // Since we can't use reflection inside EoC Server, we need a way to recognize every subclass of the `Post` class.
    // This is done using a property `post`, we can test using `isset($doc->isPost)`.
    if (empty($this->meta['isPost']))
      $this->meta['isPost'] = TRUE;

    // Same like before, but this time we use `isset($doc->useCache)` to test if the class implements the `ICache`
    // interface.
    if (empty($this->meta['useCache']))
      $this->meta['useCache'] = TRUE;

    // Now we call the parent implementation.
    parent::save();

    if (!$deferred) {
      // Marks the start of a transaction block. Subsequent commands will be queued for atomic execution using `exec()`.
      $this->redis->multi();

      $this->reindex();

      $this->redis->exec();
    }
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
    return ($this->isProtected() && $this->meta['protection'] == self::CLOSED_PL) ? TRUE : FALSE;
  }

  /**
   * @brief Returns `true` if the post is locked.
   */
  public function isLocked() {
    return ($this->isProtected() && $this->protection == self::LOCKED_PL) ? TRUE : FALSE;
  }

  //!@}


  /** @name Visibility Methods */
  //!@{

  /**
   * @brief Makes the post to be listed.
   * @retval bool
   */
  public function isVisible() {
    return $this->meta['visible'];
  }


  /**
   * @brief Hides the post.
   */
  public function hide() {
    $this->meta['visible'] = FALSE;
  }


  /**
   * @brief Makes the post to be listed.
   */
  public function show() {
    $this->meta['visible'] = TRUE;
  }

  //!@}


  /**
   * @copydoc Versionable::approve()
   */
  public function approve($update = FALSE) {
    parent::approve();

    if (!isset($this->publishedAt) or $update)
      $this->publishedAt = time();

    // Used to group by year, month and day.
    $this->meta['year'] = date("Y", $this->publishedAt);
    $this->meta['month'] = date("m", $this->publishedAt);
    $this->meta['day'] = date("d", $this->publishedAt);

    $this->meta['slug'] = Helper\Text::slug($this->title);
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
   * @brief Gets the post slug.
   * @retval string
   */
  public function getSlug() {
    return $this->meta['slug'];
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


  /** @name Indexing Methods */
  //!@{

  /**
   * @brief Adds an ID, using the provided score, to multiple sets of the Redis db.
   * @param[in] string $set The name of the base Redis set.
   * @param[in] \DateTime $date A date.
   * @param[in] string $id The post ID.
   * @param[in] int $score The score.
   */
  private function zMultipleAdd($set, \DateTime $date, $id, $score) {
    $this->redis->zAdd($set, $score, $id);
    $this->redis->zAdd($set . $date->format('_Ymd'), $score, $id);
    $this->redis->zAdd($set . $date->format('_Ym'), $score, $id);
    $this->redis->zAdd($set . $date->format('_Y'), $score, $id);
    $this->redis->zAdd($set . $date->format('_Y_w'), $score, $id);
  }


  /**
   * @brief Removes an ID from multiple sets of the Redis db.
   * @param[in] string $set The name of the base Redis set.
   * @param[in] \DateTime $date A date.
   * @param[in] string $id The post ID.
   */
  private function zMultipleRem($set, \DateTime $date, $id) {
    $this->redis->zRem($set, $id);
    $this->redis->zRem($set . $date->format('_Ymd'), $id);
    $this->redis->zRem($set . $date->format('_Ym'), $id);
    $this->redis->zRem($set . $date->format('_Y'), $id);
    $this->redis->zRem($set . $date->format('_Y_w'), $id);
  }


  /**
   * @brief Adds the post ID, using the provided score, to the specified Redis set.
   * @param[in] string $set The name of the Redis set.
   * @param[in] int $score The score.
   */
  protected function zAdd($set, $score) {
    if (!$this->isVisible()) return;

    $id = $this->unversionId;

    // Order set with all the posts.
    if (static::INDEX)
      $this->redis->zAdd($set . 'post', $score, $id);

    // Order set with all the posts of a specific type.
    $this->redis->zAdd($set . $this->type, $score, $id);

    if ($this->isMetadataPresent('tags')) {
      $tags = $this->uniqueMasters();

      foreach ($tags as $tagId) {
        // Order set with all the posts related to a specific tag.
        if (static::INDEX)
          $this->redis->zAdd($set . $tagId . '_' . 'post', $score, $id);

        // Order set with all the posts of a specific type, related to a specific tag.
        $this->redis->zAdd($set . $tagId . '_' . $this->type, $score, $id);
      }
    }
  }


  /**
   * @brief Removes the post ID from the specified Redis set.
   * @param[in] string $set The name of the Redis set.
   */
  protected function zRem($set) {
    $id = $this->unversionId;

    // Order set with all the posts.
    $this->redis->zRem($set.'post', $id);

    // Order set with all the posts of a specific type.
    $this->redis->zRem($set.$this->type, $id);

    foreach ($this->zRemTags as $tagId) {
      // Order set with all the posts related to a specific tag.
      if (static::INDEX)
        $this->redis->zRem($set . $tagId . '_' . 'post', $id);

      // Order set with all the posts of a specific type, related to a specific tag.
      $this->redis->zRem($set . $tagId . '_' . $this->type, $id);
    }
  }


  /**
   * @brief Adds the post ID, using the provided score, to the specified Redis set and its related subsets.
   * @param[in] string $set The name of the Redis set.
   * @param[in] \DateTime $date Use this date to create multiple subsets.
   * @param[in] int $score The score.
   */
  protected function zAddSpecial($set, \DateTime $date, $score) {
    if (!$this->isVisible()) return;

    $id = $this->unversionId;

    // Order set with all the posts.
    if (static::INDEX)
      $this->zMultipleAdd($set . 'post', $date, $id, $score);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->zMultipleAdd($set . $this->type, $date, $id, $score);

    if ($this->isMetadataPresent('tags')) {
      $tags = $this->uniqueMasters();

      foreach ($tags as $tagId) {
        // Order set with all the posts related to a specific tag.
        if (static::INDEX)
          $this->zMultipleAdd($set . $tagId . '_' . 'post', $date, $id, $score);

        // Order set with all the post of a specific type, related to a specific tag.
        $this->zMultipleAdd($set . $tagId . '_' . $this->type, $date, $id, $score);
      }
    }
  }


  /**
   * @brief Removes the post ID from the specified Redis set and its related subsets.
   * @param[in] string $set The name of the Redis set.
   * @param[in] \DateTime $date Use this date to create multiple subsets.
   */
  protected function zRemSpecial($set, \DateTime $date) {
    $id = $this->unversionId;

    // Order set with all the posts.
    if (static::INDEX)
      $this->zMultipleRem($set . 'post', $date, $id);

    // Order set with all the posts of a specific type.
    $this->zMultipleRem($set . $this->type, $date, $id);

    foreach ($this->zRemTags as $tagId) {
      // Order set with all the posts related to a specific tag.
      if (static::INDEX)
        $this->zMultipleRem($set . $tagId . '_' . 'post', $date, $id);

      // Order set with all the post of a specific type, related to a specific tag.
      $this->zMultipleRem($set . $tagId . '_' . $this->type, $date, $id);
    }
  }
  
  
  /**
   * @brief Adds the post to the newest index.
   */
  public function zAddNewest() {
    $this->zAdd(self::NEW_SET, $this->publishedAt);
  }


  /**
   * @brief Removes the post from the newest index.
   */
  public function zRemNewest() {
    $this->zRem(self::NEW_SET);
  }


  /**
   * @brief Adds the post to the popular index.
   */
  public function zAddPopular() {
    $config = $this->di['config'];

    $popularity = ($this->getScore() * $config->scoring->voteCoefficient) +
                  ($this->getRepliesCount() * $config->scoring->replyCoefficient) +
                  ($this->getHitsCount() * $config->scoring->hitCoefficient);

    $date = (new \DateTime())->setTimestamp($this->publishedAt);
    $this->zAddSpecial(self::POP_SET, $date, $popularity);
  }


  /**
   * @brief Removes the post from the popular index.
   */
  public function zRemPopular() {
    $date = (new \DateTime())->setTimestamp($this->publishedAt);
    $this->zRemSpecial(self::POP_SET, $date);
  }


  /**
   * @brief Adds the post to the active index.
   */
  public function zAddActive() {
    if (!$this->isVisible()) return;

    $id = $this->unversionId;
    $timestamp = $this->getLastUpdate();

    // Order set with all the posts.
    if (static::INDEX)
      $this->redis->zAdd(self::ACT_SET . 'post', $timestamp, $id);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->redis->zAdd(self::ACT_SET . $this->type, $timestamp, $id);

    if ($this->isMetadataPresent('tags')) {
      $tags = $this->uniqueMasters();

      foreach ($tags as $tagId) {
        // Filters posts which should appear on the home page.
        if (static::INDEX) {
          // Order set with all the posts related to a specific tag.
          $this->redis->zAdd(self::ACT_SET . $tagId . '_' . 'post', $timestamp, $id);

          // Used to get a list of tags recently updated.
          $this->redis->zAdd(self::ACT_SET . 'tags' . '_' . 'post', $timestamp, $tagId);
        }

        // Order set with all the posts of a specific type, related to a specific tag.
        $this->redis->zAdd(self::ACT_SET . $tagId . '_' . $this->type, $timestamp, $id);

        // Used to get a list of tags, in relation to a specific type, recently updated.
        $this->redis->zAdd(self::ACT_SET . 'tags' . '_' . $this->type, $timestamp, $tagId);
      }
    }
  }


  /**
   * @brief Removes the post from the active index.
   */
  public function zRemActive() {
    $id = $this->unversionId;

    // Order set with all the posts.
    $this->redis->zRem(self::ACT_SET . 'post', $id);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->redis->zRem(self::ACT_SET . $this->type, $id);

    foreach ($this->zRemTags as $tagId) {
      // Filters posts which should appear on the home page.
      if (static::INDEX) {
        // Order set with all the posts related to a specific tag.
        $this->redis->zRem(self::ACT_SET . $tagId . '_' . 'post', $id);

        // Used to get a list of tags recently updated.
        $this->redis->zRem(self::ACT_SET . 'tags' . '_' . 'post', $tagId);
      }

      // Order set with all the posts of a specific type, related to a specific tag.
      $this->redis->zRem(self::ACT_SET . $tagId . '_' . $this->type, $id);

      // Used to get a list of tags, in relation to a specific type, recently updated.
      $this->redis->zRem(self::ACT_SET . 'tags' . '_' . $this->type, $tagId);
    }
  }


  /**
   * @brief Removes the post ID from the indexes.
   */
  public function deindex() {
    $this->zRemNewest();
    $this->zRemPopular();
    $this->zRemActive();
  }


  /**
   * @brief Adds the post ID to the indexes.
   */
  public function index() {
    // We are only indexing current versions.
    if (!$this->state->isCurrent())
      return;

    $this->zAddNewest();
    $this->zAddPopular();
    $this->zAddActive();
  }


  /**
   * @brief Performs deindex then reindex.
   */
  public function reindex() {
    $this->deindex();
    $this->reindex();
  }

  //!@}


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


  /** @name Tagging Methods */
  // @{

  /**
   * @brief Resolve the synonyms and returns only unique master tags.
   * @retval array
   */
  protected function uniqueMasters() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce();
    $masters = $this->couch->queryView("tags", "synonyms", $this->meta['tags'], $opts);
    return array_unique(array_column($masters->asArray(), 'value'));
  }


  /**
   * @brief Removes all associated tags.
   */
  public function resetTags() {
    $this->unsetMetadata('tags');
  }


  /**
   * @brief Adds the specified tag to the list of tags.
   * @attention Don't use this method even if it's public, unless you know what are you doing.
   * @param[in] int $tagId The tag uuid.
   */
  public function addTagId($tagId) {
    $this->meta['tags'][] = Helper\Text::unversion($tagId);
  }


  /**
   * @brief Adds many tags at once to the list of tags.
   * @param[in] array $names An array of strings, the tag names.
   */
  public function addMultipleTagsAtOnce(array $names) {
    $names = array_unique($names);

    $opts = new ViewQueryOpts();
    $opts->includeMissingKeys();
    $rows = $this->couch->queryView("tags", "byNameSpecial", $names, $opts)->asArray();

    foreach ($rows as $row) {
      // A tag hasn't been found, so creates it.
      if (is_null($row['id'])) {
        $tag = Tag::create();
        $tag->name = $row['key'];
        $tag->creatorId = $this->creatorId;
        $tag->approve();
        $tag->save();

        $this->addTagId($tag->unversionId);
      }
      else
        $this->addTagId(Helper\Text::unversion($row['id']));
    }
  }


  /**
   * @brief Gets the associated list of tags.
   */
  public function getTags() {
    if ($this->isMetadataPresent('tags')) {
      $ids = $this->uniqueMasters();

      $opts = new ViewQueryOpts();
      $opts->doNotReduce();
      return $this->couch->queryView("tags", "allNames", $ids, $opts);
    }
    else
      return [];
  }


  // In case of a Post we add the tags, so we can obtain the favorites of a specific tag.
  /*
  if (method_exists($item, 'getTags')) {
    $tags = array_column($item->getTags(), 'id');
    ArrayHelper::unversion($tags);
    $instance->meta['itemTags'] = $tags;
  }
  */

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


  public function getPublishedAt() {
    return $this->meta['publishedAt'];
  }


  public function issetPublishedAt() {
    return isset($this->meta['publishedAt']);
  }


  public function setPublishedAt($value) {
    $this->meta['publishedAt'] = $value;
  }


  public function unsetPublishedAt() {
    if ($this->isMetadataPresent('publishedAt'))
      unset($this->meta['publishedAt']);
  }

  //! @endcond

}