<?php

//! @file Post.php
//! @brief This file contains the Post class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Extension;
use PitPress\Helper\Time;


//! @brief This class is used to represent a generic entry, a content created by a user.
//! @details Every post is versioned into the database, has tags and also a owner, who created the entry.
//! @nosubgrouping
abstract class Post extends Item implements Extension\ICount, Extension\IStar, Extension\IVote, Extension\ISubscribe {
  use Extension\TCount, Extension\TStar, Extension\TVote, Extension\TSubscribe;


  //! @brief Constructor.
  public function __construct() {
    parent::__construct();
    $this->meta['supertype'] = 'post';
    $this->meta['section'] = $this->getSection();
  }


  //! @brief Saves the post to the database.
  public function save() {
    $this->permalink = $this->getPermalink();
    $this->prettyDate = $this->getPrettyDate();
    $this->prettyTitle = $this->getPrettyTitle();
    $this->prettyUrl = $this->getPrettyUrl();

    parent::save();
  }


  //! @brief Gets the resource permanent link.
  //! @return string
  public function getPermalink() {
    return "/".$this->getSection()."/".$this->id;
  }


  public function getPrettyDate() {
    return date("Y/m/d", $this->publishingDate);
  }


  public function getPrettyTitle() {
    $title = preg_replace('/[^a-z0-9]/i',' ', $this->title);
    return str_replace(" ", "-", $title);
  }


  public function getPrettyUrl() {
    return $this->getPrettyDate()."/".$this->getSection()."/".$this->getPrettyTitle().".html";
  }


  //! @brief The post belongs to this section.
  //! @return string
  abstract public function getSection();


  //! @brief Gets the publishing type.
  //! @return string
  abstract public function getPublishingType();


  //! @brief Returns a measure of the time passed since the publishing date. In case is passed more than a day, returns
  //! a human readable date.
  //! @return string
  public function whenHasBeenPublished() {
    return Time::when($this->publishingDate);
  }


  //! @brief Gets the number of the answer or comments.
  public function getReplaysCount() {

  }


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

    $result = $this->couch->queryView("classifications", "perPost", NULL, $opts);

    $keys = [];
    foreach ($result['rows'] as $classification)
      $keys[] = $classification['value'];

    $opts->reset();
    $opts->doNotReduce();

    return $this->couch->queryView("tags", "all", $keys, $opts);
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