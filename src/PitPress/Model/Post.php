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


/**
 * @brief This class is used to represent a generic entry, a content created by a user.
 * @details Every post is versioned into the database, has tags and also a owner, who created the entry.
 * @nosubgrouping
 */
abstract class Post extends Versionable implements Extension\ICount, Extension\IStar, Extension\IVote, Extension\ISubscribe {
  use Extension\TCount, Extension\TStar, Extension\TVote, Extension\TSubscribe;
  use Property\TDescription;


  /**
   * @copydoc
   */
  public function save() {
    $this->meta['supertype'] = 'post';
    $this->meta['slug'] = $this->getSlug();

    // Used to group by year, month and day.
    $this->meta['year'] = date("Y", $this->publishedAt);
    $this->meta['month'] = date("m", $this->publishedAt);
    $this->meta['day'] = date("d", $this->publishedAt);

    parent::save();
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
    $title = preg_replace('~[^\\pL\d]+~u', '-', $this->title);
    $title = trim($title, '-');
    $title = iconv('utf-8', 'ASCII//TRANSLIT', $title);
    $title = strtolower($title);
    return preg_replace('~[^-\w]+~', '', $title);
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
    return "/".date("Y/m/d", $this->publishedAt)."/".$this->getSlug();
  }


  /** @name Replaying Methods */
  //!@{

  /**
   * @brief Get the post replays, answers, in case of a question, else comments
   */
  public function getReplies() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setStartKey([$this->getUnversionId(), Couch::WildCard()])->setEndKey([$this->getUnversionId()])->includeDocs();
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
    return $this->couch->queryView("replies", "perPost", [$this->getUnversionId()], $opts)->getReducedValue();
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
  public function addTag($tagId) {
    $this->meta['tags'][] = $tagId;
  }


  /**
   * @brief Adds many tags at once to the list of tags.
   */
  public function addMultipleTagsAtOnce(array $tags) {
    $this->meta['tags'] = array_unique(array_merge($this->meta['tags'], $tags));
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