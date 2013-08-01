<?php

//! @file Tag.php
//! @brief This file contains the Tag class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Tag;


use PitPress\Model\User\User;
use PitPress\Model\VersionedItem;


//! @brief
//! @nosubgrouping
class Tag extends VersionedItem {

  //! @name ModerationTrait
  //@{
  const SUBMITTED_STATE = "submitted"; //!< The item has been submitted for publishing.
  const PUBLISHED_STATE = "published"; //!< The item has been published.
  const REJECTED_STATE = "rejected"; //!< The item has been rejected.
  //@}


  public function ignore(User $currentUser) {
  }


  public function unignore(User $currentUser) {
  }


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

}