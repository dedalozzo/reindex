<?php

//! @file Tag.php
//! @brief This file contains the Tag class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Tag;


use PitPress\Model\VersionedItem;
use PitPress\Model\Helper;


//! @brief
//! @nosubgrouping
class Tag extends VersionedItem {
  use Helper\SubscriptionTrait;
  use Helper\ViewTrait;


  //! @name ModerationTrait
  //@{
  const SUBMITTED_STATE = "submitted"; //!< The item has been submitted for publishing.
  const PUBLISHED_STATE = "published"; //!< The item has been published.
  const REJECTED_STATE = "rejected"; //!< The item has been rejected.
  //@}


  //! @brief Gets the item state.
  public function getState() {
    return $this->meta['state'];
  }


  public function getFollowersCount() {

  }


  public function getName() {
    return $this->meta['name'];
  }


  public function issetName() {
    return isset($this->meta['name']);
  }


  public function setName($value) {
    $this->meta['name'] = $value;
  }


  public function unsetName() {
    if ($this->isMetadataPresent('name'))
      unset($this->meta['name']);
  }


  public function getExcerpt() {
    return $this->meta['excerpt'];
  }


  public function issetExcerpt() {
    return isset($this->meta['excerpt']);
  }


  public function setExcerpt($value) {
    $this->meta["excerpt"] = $value;
  }


  public function unsetExcerpt() {
    if ($this->isMetadataPresent('excerpt'))
      unset($this->meta['excerpt']);
  }


  public function getBody() {
    return $this->meta["body"];
  }


  public function issetBody() {
    return isset($this->meta['body']);
  }


  public function setBody($value) {
    $this->meta["body"] = $value;
  }


  public function unsetBody() {
    if ($this->isMetadataPresent('body'))
      unset($this->meta['body']);
  }

}