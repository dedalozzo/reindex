<?php

//! @file Post.php
//! @brief This file contains the Post class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


use PitPress\Model\User\User;


//! @brief This class is used to represent a generic entry, a content created by a user.
//! @details Every post is versioned into the database, has tags and also a owner, who created the entry.
//! @nosubgrouping
abstract class Post extends VersionedItem {
  use Helper\SubscriptionTrait;
  use Helper\ViewTrait;


  //! @brief Constructor.
  public function __construct() {
    parent::__construct();
    $this->meta['supertype'] = 'post';
  }


  public function getPublishingDate() {
    return $this->meta["publishingDate"];
  }


  //! @brief Gets the item permanent link.
  public function getPermalink() {

  }


  //! @brief Gets the number of the answer or comments.
  public function getReplaysCount() {

  }


  //! @brief Gets the associated tags list.
  public function getTags() {

  }


  //! @brief Removes all tags.
  public function resetTags() {

  }


  //! @brief Adds the specified tag to the tags list.
  public function addTag() {

  }


  //! @brief Adds many tags at once to the tags list.
  public function addMultipleTagsAtOnce() {

  }


  //! @brief Adds the item to the favourites list of the current user.
  public function star(User $currentUser) {

  }


  //! @brief Removes the item from the favourites list of the current user.
  public function unstar(User $currentUser) {

  }


  public function setOwnerId($value) {
    $this->meta["ownerId"] = $value;
  }


  public function getOwnerId() {
    return $this->meta["ownerId"];
  }


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

}