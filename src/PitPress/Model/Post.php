<?php

/**
 * @file Post.php
 * @brief This file contains the Post class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Extension;
use PitPress\Property;
use PitPress\Helper;
use PitPress\Enum;
use PitPress\Exception;

use Phalcon\DI;


/**
 * @brief This class is used to represent a generic entry, a content created by a user.
 * @details Every post is versioned into the database, has tags and also a owner, who created the entry.
 * @nosubgrouping
 */
abstract class Post extends Versionable implements Extension\ICount, Extension\IStar, Extension\IVote, Extension\ISubscribe {
  use Extension\TCount, Extension\TStar, Extension\TVote, Extension\TSubscribe;
  use Property\TExcerpt, Property\TBody, Property\TDescription;

  /** @name Redis Set Names */
  //!@{

  const POP_SET = 'pop_';
  const UPD_SET = 'upd_';

  //!@}

  /** @name Protection Levels */
  //!@{

  const CLOSED_PL = 'closed'; //!< The post is closed.
  const LOCKED_PL = 'locked'; //!< The post is locked.

  //!@}

  const INDEX = FALSE; //!< A generic post doesn't appear on the home page.

  // Since the user can add new tags in a second moment, we must store in a member the original tags, otherwise the zRem
  // methods will not work properly.
  private $zRemTags;

  protected $markdown; // Stores the Markdown parser instance.
  protected $monolog; // Stores the logger instance.


  public function __construct() {
    parent::__construct();
    $this->markdown = $this->di['markdown'];
    $this->monolog = $this->di['monolog'];

    $this->meta['supertype'] = 'post';
    $this->meta['index'] = static::INDEX; // We use static because the INDEX constant is overridden by subclasses.
    $this->meta['visible'] = TRUE;

    $this->zRemTags = ($this->isMetadataPresent('tags')) ? $this->getMetadata('tags') : [];
  }


  /**
   * @brief Generates the post slug.
   * @return string
   */
  protected function buildSlug() {
    $title = preg_replace('~[^\\pL\d]+~u', '-', $this->title);
    $title = trim($title, '-');
    $title = Helper\Text::convertCharset($title, FALSE, 'utf-8', 'ASCII//TRANSLIT');
    $title = strtolower($title);
    return preg_replace('~[^-\w]+~', '', $title);
  }


  /**
   * @brief Saves the post.
   * @param[in] bool $deferred When `true` doesn't update the post popularity.
   */
  public function save($deferred = FALSE) {
    $this->html = $this->markdown->parse($this->body);
    $purged = Helper\Text::purge($this->html);
    $this->excerpt = Helper\Text::truncate($purged);

    parent::save();

    $this->zRemLastUpdate();
    $this->zRemPopularity();

    if ($this->isCurrent()) {
      $this->zAddLastUpdate();

      if (!$deferred)
        $this->zAddPopularity();
    }
  }


  /** @name Protection Methods */
  //!@{

  /**
   * @brief Returns `true` if the post has some kind of protection.
   */
  public function isProtected() {
    return $this->isMetadataPresent('protected');
  }


  /**
   * @brief Closes the post. No more answers or comments can be added.
   * @see http://meta.stackexchange.com/questions/10582/what-is-a-closed-or-on-hold-question
   */
  public function close() {
    if ($this->user->isGuest()) throw new Exception\NoUserLoggedInException('Nessun utente loggato nel sistema.');
    if ($this->isClosed()) return;

    if ($this->user->isModerator())
      if ($this->isCurrent() or $this->isDraft())
        $this->meta['protection'] = self::CLOSED_PL;
      else
        throw new Exception\IncompatibleStatusException("Stato incompatible con l'operazione richiesta.");
    else
      throw new Exception\NotEnoughPrivilegesException("Privilegi di accesso insufficienti.");
  }


  /**
   * @brief Returns `true` if the post is closed.
   */
  public function isClosed() {
    return ($this->meta['protection'] == self::CLOSED_PL) ? TRUE : FALSE;
  }


  /**
   * @brief Locks the post.
   * @see http://meta.stackexchange.com/questions/22228/what-is-a-locked-post
   */
  public function lock() {
    if ($this->user->isGuest()) throw new Exception\NoUserLoggedInException('Nessun utente loggato nel sistema.');
    if ($this->isLocked()) return;

    if ($this->user->isModerator())
      if ($this->isCurrent() or $this->isDraft()) {
        $this->meta['protection'] = self::LOCKED_PL;
        $this->meta['protectorId'] = $this->user->id;
      }
      else
        throw new Exception\IncompatibleStatusException("Stato incompatible con l'operazione richiesta.");
    else
      throw new Exception\NotEnoughPrivilegesException("Privilegi di accesso insufficienti.");
  }


  /**
   * @brief Returns `true` if the post is locked.
   */
  public function isLocked() {
    return ($this->protection == self::LOCKED_PL) ? TRUE : FALSE;
  }


  /**
   * @brief Removes the post protection.
   */
  public function unprotect() {
    if ($this->user->isGuest()) throw new Exception\NoUserLoggedInException('Nessun utente loggato nel sistema.');
    if (!$this->isProtected()) return;

    if ($this->user->isAdmin() or ($this->user->isModerator() && $this->user->match($this->protectorId)))
      if ($this->isCurrent() or $this->isDraft()) {
        $this->unsetMetadata('protection');
        $this->unsetMetadata('protectorId');
      }
      else
        throw new Exception\IncompatibleStatusException("Stato incompatible con l'operazione richiesta.");
    else
      throw new Exception\NotEnoughPrivilegesException("Privilegi di accesso insufficienti.");
  }


  /**
   * @brief Returns the id of the user who protected the post.
   * @return string
   */
  public function getProtectorId() {
    return ($this->isMetadataPresent('protectorId')) ? $this->meta['protectorId'] : NULL;
  }

  //!@}


  /** @name Visibility Methods */
  //!@{

  /**
   * @brief Hides the post.
   */
  public function hide() {
    if ($this->user->isGuest()) throw new Exception\NoUserLoggedInException('Nessun utente loggato nel sistema.');
    if (!$this->isVisible()) return;

    if ($this->user->isAdmin())
      if ($this->isCurrent() or $this->isDraft())
        $this->meta['visible'] = FALSE;
      else
        throw new Exception\IncompatibleStatusException("Stato incompatible con l'operazione richiesta.");
    else
      throw new Exception\NotEnoughPrivilegesException("Privilegi di accesso insufficienti.");
  }


  /**
   * @brief Makes the post to be listed.
   */
  public function show() {
    if ($this->user->isGuest()) throw new Exception\NoUserLoggedInException('Nessun utente loggato nel sistema.');
    if ($this->isVisible()) return;

    if ($this->user->isAdmin())
      if ($this->isCurrent() or $this->isDraft())
        $this->meta['visible'] = TRUE;
      else
        throw new Exception\IncompatibleStatusException("Stato incompatible con l'operazione richiesta.");
    else
      throw new Exception\NotEnoughPrivilegesException("Privilegi di accesso insufficienti.");
  }


  /**
   * @brief Makes the post to be listed.
   * @return bool
   */
  public function isVisible() {
    return $this->meta['visible'];
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

    $this->meta['slug'] = $this->buildSlug();
  }


  /**
   * @brief Returns a measure of the time passed since the publishing date. In case is passed more than a day, returns
   * a human readable date.
   * @return string
   */
  public function whenHasBeenPublished() {
    return Helper\Time::when($this->publishedAt);
  }


  /**
   * @brief Gets the post slug.
   * @return string
   */
  public function getSlug() {
    return $this->meta['slug'];
  }


  /**
   * @brief Gets the resource permanent link.
   * @return string
   */
  public function getPermalink() {
    return "/".$this->id;
  }


  /**
   * @brief Gets the post URL.
   * @return string
   */
  public function getHref() {
    return Helper\Url::build($this->publishedAt, $this->getSlug());
  }


  /** @name Popularity Management Methods */
  //!@{

  /**
   * @brief Adds the post score to the Redis db.
   * @param[in] string $set The name of the Redis set.
   * @param[in] \DateTime $date The modification date.
   * @param[in] int $score The score.
   * @param[in] The post id.
   */
  protected function zAddScore($set, \DateTime $date, $score, $id) {
    $this->redis->zAdd($set, $score, $id);
    $this->redis->zAdd($set.$date->format('_Ymd'), $score, $id);
    $this->redis->zAdd($set.$date->format('_Ym'), $score, $id);
    $this->redis->zAdd($set.$date->format('_Y'), $score, $id);
    $this->redis->zAdd($set.$date->format('_Y_w'), $score, $id);
  }


  /**
   * @brief Removes the post score from the Redis db.
   * @param[in] string $set The name of the Redis set.
   * @param[in] \DateTime $date The modification date.
   * @param[in] The post id.
   */
  protected function zRemScore($set, \DateTime $date, $id) {
    $this->redis->zRem($set, $id);
    $this->redis->zRem($set.$date->format('_Ymd'), $id);
    $this->redis->zRem($set.$date->format('_Ym'), $id);
    $this->redis->zRem($set.$date->format('_Y'), $id);
    $this->redis->zRem($set.$date->format('_Y_w'), $id);
  }


  /**
   * @brief Adds the post popularity to the Redis db.
   */
  public function zAddPopularity() {
    $config = $this->di['config'];

    $date = (new \DateTime())->setTimestamp($this->publishedAt);
    $popularity = ($this->getScore() * $config->scoring->voteCoefficient) + ($this->getRepliesCount() * $config->scoring->replyCoefficient) + ($this->getHitsCount() * $config->scoring->hitCoefficient);
    $id = $this->unversionId;

    // Order set with all the posts.
    if (static::INDEX)
      $this->zAddScore(self::POP_SET.'post', $date, $popularity, $id);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->zAddScore(self::POP_SET.$this->type, $date, $popularity, $id);

    if ($this->isMetadataPresent('tags')) {
      $tags = $this->meta['tags'];

      foreach ($tags as $tagId) {
        // Order set with all the posts related to a specific tag.
        if (static::INDEX)
          $this->zAddScore(self::POP_SET.$tagId.'_'.'post', $date, $popularity, $id);

        // Order set with all the post of a specific type, related to a specific tag.
        $this->zAddScore(self::POP_SET.$tagId.'_'.$this->type, $date, $popularity, $id);
      }
    }
  }


  /**
   * @brief Removes the post popularity from the Redis db.
   */
  public function zRemPopularity() {
    $date = (new \DateTime())->setTimestamp($this->publishedAt);
    $id = $this->unversionId;

    // Order set with all the posts.
    if (static::INDEX)
      $this->zRemScore(self::POP_SET.'post', $date, $id);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->zRemScore(self::POP_SET.$this->type, $date, $id);

    foreach ($this->zRemTags as $tagId) {
      // Order set with all the posts related to a specific tag.
      if (static::INDEX)
        $this->zRemScore(self::POP_SET . $tagId . '_' . 'post', $date, $id);

      // Order set with all the post of a specific type, related to a specific tag.
      $this->zRemScore(self::POP_SET . $tagId . '_' . $this->type, $date, $id);
    }
  }

  //!@}


  /** @name Last Update Management Methods */
  //!@{

  /**
   * @brief Adds the post last update timestamp to the Redis db.
   */
  public function zAddLastUpdate($timestamp = NULL) {
    if (is_null($timestamp))
      $timestamp = $this->modifiedAt;

    $id = $this->unversionId;

    // Order set with all the posts.
    if (static::INDEX)
      $this->redis->zAdd(self::UPD_SET.'post', $timestamp, $id);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->redis->zAdd(self::UPD_SET.$this->type, $timestamp, $id);

    if ($this->isMetadataPresent('tags')) {
      $tags = $this->meta['tags'];

      foreach ($tags as $tagId) {
        // Filters posts which should appear on the home page.
        if (static::INDEX) {
          // Order set with all the posts related to a specific tag.
          $this->redis->zAdd(self::UPD_SET . $tagId . '_' . 'post', $timestamp, $id);

          // Used to get a list of tags recently updated.
          $this->redis->zAdd(self::UPD_SET . 'tags' . '_' . 'post', $timestamp, $tagId);
        }

        // Order set with all the posts of a specific type, related to a specific tag.
        $this->redis->zAdd(self::UPD_SET.$tagId.'_'.$this->type, $timestamp, $id);

        // Used to get a list of tags, in relation to a specific type, recently updated.
        $this->redis->zAdd(self::UPD_SET.'tags'.'_'.$this->type, $timestamp, $tagId);
      }
    }
  }


  /**
   * @brief Removes the post last update timestamp from the Redis db.
   */
  public function zRemLastUpdate() {
    $id = $this->unversionId;

    // Order set with all the posts.
    $this->redis->zRem(self::UPD_SET.'post', $id);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->redis->zRem(self::UPD_SET.$this->type, $id);

    foreach ($this->zRemTags as $tagId) {
      // Filters posts which should appear on the home page.
      if (static::INDEX) {
        // Order set with all the posts related to a specific tag.
        $this->redis->zRem(self::UPD_SET . $tagId . '_' . 'post', $id);

        // Used to get a list of tags recently updated.
        $this->redis->zRem(self::UPD_SET . 'tags' . '_' . 'post', $tagId);
      }

      // Order set with all the posts of a specific type, related to a specific tag.
      $this->redis->zRem(self::UPD_SET.$tagId.'_'.$this->type, $id);

      // Used to get a list of tags, in relation to a specific type, recently updated.
      $this->redis->zRem(self::UPD_SET.'tags'.'_'.$this->type, $tagId);
    }
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
   * @brief Removes all associated tags.
   */
  public function resetTags() {
    $this->unsetMetadata('tags');
  }


  /**
   * @brief Adds the specified tag to the list of tags.
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
    $rows = $this->couch->queryView("tags", "byName", $names, $opts)->asArray();

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
    $opts = new ViewQueryOpts();
    $opts->doNotReduce();

    if ($this->isMetadataPresent('tags'))
      return $this->couch->queryView("tags", "allNames", $this->meta['tags'], $opts);
    else
      return [];
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