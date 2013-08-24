<?php

//! @file Item.php
//! @brief This file contains the Item class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


use ElephantOnCouch\Opt\ViewQueryOpts;


//! @brief A generic content created by a user.
//! @nosubgrouping
abstract class Item extends Storable {


  public function getOwner() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey($this->userId);

    $result = $this->couch->queryView("users", "allNames", NULL, $opts);

    if (!empty($result['rows']))
      return $result['rows'][0]['value'];
    elseif (isset($this->username))
      return $this->username;
    else
      return "anonimo";
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