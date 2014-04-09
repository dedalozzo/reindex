<?php

//! @file Item.php
//! @brief This file contains the Item class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Extension;
use PitPress\Helper\Time;


//! @brief A generic content created by a user.
//! @nosubgrouping
abstract class Item extends Storable {
  use Extension\TVersion;


  public function save() {
    // Put your code here.

    parent::save();
  }


  //! @brief Returns a measure of the time passed since the publishing date. In case is passed more than a day, returns
  //! a human readable date.
  //! @return string
  public function whenHasBeenPublished() {
    return Time::when($this->publishingDate);
  }


  //! @brief Returns the author name.
  public function getDisplayName() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setKey($this->userId);
    return $this->couch->queryView("users", "allNames", NULL, $opts)['rows'][0]['value'][0];
  }


  //! @brief Builds the gravatar uri.
  //! @return string
  public function getGravatar() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setKey($this->userId);
    $email = $this->couch->queryView("users", "allNames", NULL, $opts)['rows'][0]['value'][1];
    return 'http://gravatar.com/avatar/'.md5(strtolower($email)).'?d=identicon';
  }


  //! @cond HIDDEN_SYMBOLS

  public function getUserId() {
    return $this->meta["userId"];
  }


  public function issetUserId() {
    return isset($this->meta['userId']);
  }


  public function setUserId($value) {
    $this->meta["userId"] = $value;
  }


  public function unsetUserId() {
    if ($this->isMetadataPresent('userId'))
      unset($this->meta['userId']);
  }


  public function getUsername() {
    return $this->meta['username'];
  }


  public function issetUsername() {
    return isset($this->meta['username']);
  }


  public function setUsername($value) {
    $this->meta['username'] = $value;
  }


  public function unsetUsername() {
    if ($this->isMetadataPresent('username'))
      unset($this->meta['username']);
  }


  public function getPublishingDate() {
    return $this->meta['publishingDate'];
  }


  public function issetPublishingDate() {
    return isset($this->meta['publishingDate']);
  }


  public function setPublishingDate($value) {
    $this->meta['publishingDate'] = $value;
  }


  public function unsetPublishingDate() {
    if ($this->isMetadataPresent('publishingDate'))
      unset($this->meta['publishingDate']);
  }

  //! @endcond

}