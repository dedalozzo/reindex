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

use Phalcon\DI;


/**
 * @brief This class is used to represent a generic entry, a content created by a user.
 * @details Every post is versioned into the database, has tags and also a owner, who created the entry.
 * @nosubgrouping
 */
abstract class Post extends Versionable implements Extension\ICount, Extension\IStar, Extension\IVote, Extension\ISubscribe {
  use Extension\TCount, Extension\TStar, Extension\TVote, Extension\TSubscribe;
  use Property\TDescription;


  public function __construct() {
    parent::__construct();
    $this->meta['supertype'] = 'post';
  }


  /**
   * @brief Generates the post slug.
   * @return string
   */
  protected function buildSlug() {
    $title = preg_replace('~[^\\pL\d]+~u', '-', $this->title);
    $title = trim($title, '-');
    $title = iconv('utf-8', 'ASCII//TRANSLIT', $title);
    $title = strtolower($title);
    return preg_replace('~[^-\w]+~', '', $title);
  }


  /**
   * @copydoc Storable::save
   */
  public function save() {
    parent::save();
  }


  /**
   * @copydoc Versionable::approve
   */
  public function approve($update = FALSE) {
    parent::approve();

    if (!isset($this->publishedAt) || $update) {
      $this->publishedAt = time();

      // Used to group by year, month and day.
      $this->meta['year'] = date("Y", $this->publishedAt);
      $this->meta['month'] = date("m", $this->publishedAt);
      $this->meta['day'] = date("d", $this->publishedAt);
    }

    $this->meta['slug'] = $this->buildSlug();
  }


  /**
   * @brief Marks the document as draft.
   * @details When a user works on an article, he wants save many time the item before submit it for peer revision.
   */
  public function markAsDraft() {
    $this->meta['status'] = Enum\DocStatus::DRAFT;

    // Used to group by year, month and day.
    $this->meta['year'] = date("Y", $this->createdAt);
    $this->meta['month'] = date("m", $this->createdAt);
    $this->meta['day'] = date("d", $this->createdAt);

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


  /**
   * @brief Registers the post score.
   * @param[in] string $set The name of the Redis set.
   * @param[in] \DateTime $date The modification date.
   * @param[in] int $score The score.
   * @param[in] The post id.
   */
  protected function addScore($set, \DateTime $date, $score, $id) {
    $this->redis->zAdd($set, $score, $id);
    $this->redis->zAdd($set.$date->format('_Ymd'), $score, $id);
    $this->redis->zAdd($set.$date->format('_Ym'), $score, $id);
    $this->redis->zAdd($set.$date->format('_Y'), $score, $id);
    $this->redis->zAdd($set.$date->format('_Y_w'), $score, $id);
  }


  /**
   * @brief Updates the post popularity.
   */
  public function updatePopularity() {
    $config = $this->di['config'];

    $set = 'pop_';
    $date = (new \DateTime())->setTimestamp($this->publishedAt);
    $popularity = ($this->getScore() * $config->scoring->voteCoefficient) + ($this->getRepliesCount() * $config->scoring->replyCoefficient) + ($this->getHitsCount() * $config->scoring->hitCoefficient);
    $id = $this->unversionId;

    // Order set with all the posts.
    $this->addScore($set.'post', $date, $popularity, $id);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->addScore($set.$this->type, $date, $popularity, $id);

    foreach ($this->tags as $tag) {
      $tagId = $tag['key']; // We need the unversion identifier.

      // Order set with all the posts related to a specific tag.
      $this->addScore($set.$tagId.'_'.'post', $date, $popularity, $id);

      // Order set with all the post of a specific type, related to a specific tag.
      $this->addScore($set.$tagId.'_'.$this->type, $date, $popularity, $id);
    }
  }


  /**
   * @brief Updates the post timestamp.
   */
  public function updateTimestamp($timestamp = NULL) {
    if (is_null($timestamp))
      $timestamp = $this->modifiedAt;

    $set = 'tmp_';
    $id = $this->unversionId;

    // Order set with all the posts.
    $this->redis->zAdd($set.'post', $timestamp, $id);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->redis->zAdd($set.$this->type, $timestamp, $id);

    if ($this->isMetadataPresent('tags')) {
      $tags = $this->meta['tags'];

      foreach ($tags as $tagId) {
        // Order set with all the posts related to a specific tag.
        $this->redis->zAdd($set.$tagId.'_'.'post', $timestamp, $id);

        // Order set with all the posts of a specific type, related to a specific tag.
        $this->redis->zAdd($set.$tagId.'_'.$this->type, $timestamp, $id);

        // Used to get a list of tags recently updated.
        $this->redis->zAdd("tmp_tags".'_'.'post', $timestamp, $tagId);

        // Used to get a list of tags, in relation to a specific type, recently updated.
        $this->redis->zAdd("tmp_tags".'_'.$this->type, $timestamp, $tagId);
      }
    }
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
        $tag->userId = $this->user->id;
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

  public function getSupertype() {
    return $this->meta['supertype'];
  }


  public function issetSupertype() {
    return isset($this->meta['supertype']);
  }


  public function setSupertype($value) {
    $this->meta['supertype'] = $value;
  }


  public function unsetSupertype() {
    if ($this->isMetadataPresent('supertype'))
      unset($this->meta['supertype']);
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