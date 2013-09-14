<?php

//! @file Post.php
//! @brief This file contains the Post class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;
use ElephantOnCouch\Helper\ArrayHelper;

use PitPress\Extension;


//! @brief This class is used to represent a generic entry, a content created by a user.
//! @details Every post is versioned into the database, has tags and also a owner, who created the entry.
//! @nosubgrouping
abstract class Post extends Item implements Extension\ICount, Extension\IStar, Extension\IVote, Extension\ISubscribe {
  use Extension\TCount, Extension\TStar, Extension\TVote, Extension\TSubscribe;


  public function save() {
    $this->meta['supertype'] = 'post';
    $this->meta['section'] = $this->getSection();
    $this->meta['publishingType'] = $this->getPublishingType();
    $this->meta['slug'] = $this->getSlug();

    // Used to group by year, month and day.
    $this->meta['year'] = date("Y", $this->publishingDate);
    $this->meta['month'] = date("m", $this->publishingDate);
    $this->meta['day'] = date("d", $this->publishingDate);

    parent::save();
  }


  public function getSlug() {
    $title = preg_replace('~[^\\pL\d]+~u', '-', $this->title);
    $title = trim($title, '-');
    $title = iconv('utf-8', 'ASCII//TRANSLIT', $title);
    $title = strtolower($title);
    return preg_replace('~[^-\w]+~', '', $title);
  }


  //! @brief Gets the resource permanent link.
  //! @return string
  public function getPermalink() {
    return "/".$this->id;
  }


  //! @brief Gets the post URL.
  //! @return string
  public function getUrl() {
    return "/".date("Y/m/d", $this->publishingDate)."/".$this->getSlug();
  }


  //! @brief The post belongs to this section.
  //! @return string
  abstract public function getSection();


  //! @brief Gets the publishing type.
  //! @return string
  abstract public function getPublishingType();


  //! @name Replaying Methods
  // @{


  //! @brief Get the post replays, answers, in case of a question, else comments
  public function getReplays() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setKey($this->id)->setLimit(20);
    $rows = $this->couch->queryView("replays", "all", NULL, $opts)['rows'];

/*    foreach ($rows as $row) {
      $replay = new Replay();
      $array = ArrayHelper::fromJson($scores[$i], TRUE)['doc'];
      $score->assignArray($array);
      $replays[] = $score;
    } */
  }


  //! @brief Gets the number of the answer or comments.
  public function getReplaysCount() {

  }

  //@}


  //! @name Tagging Methods
  // @{

  //! @brief Removes all tags.
  public function resetTags() {

  }


  //! @brief Adds the specified tag to the tags list.
  public function addTag() {

  }


  //! @brief Adds many tags at once to the tags list.
  public function addMultipleTagsAtOnce() {

  }


  //! @brief Gets the associated tags list.
  public function getTags() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setKey($this->id);

    $classifications = $this->couch->queryView("classifications", "perPost", NULL, $opts)['rows'];

    $keys = [];
    foreach ($classifications as $classification)
      $keys[] = $classification['value'];

    $opts->reset();
    $opts->doNotReduce();

    return $this->couch->queryView("tags", "all", $keys, $opts)['rows']; // Tags.
  }

  //@}


  //! @cond HIDDEN_SYMBOLS

  public function getTitle() {
    return $this->meta['title'];
  }


  public function issetTitle() {
    return isset($this->meta['title']);
  }


  public function setTitle($value) {
    $this->meta['title'] = $value;
  }


  public function unsetTitle() {
    if ($this->isMetadataPresent('title'))
      unset($this->meta['title']);
  }

  //! @endcond

}