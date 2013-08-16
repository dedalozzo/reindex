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
  protected $di; // Stores the default Dependency Injector.
  protected $couch; // Stores the ElephantOnCouch client instance.
  protected $redis; // Stores the Redis client instance.


  public function getOwnerDisplayName() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey($this->ownerId);

    $result = $this->couch->queryView("users", "allNames", NULL, $opts)->getBodyAsArray();

    if (!empty($result['rows']))
      return $result['rows'][0]['value'];
    elseif (isset($this->creator))
      return $this->creator;
    else
      return "anonimo";
  }


  //! @cond HIDDEN_SYMBOLS

  public function getOwnerId() {
    return $this->meta["ownerId"];
  }


  public function issetOwnerId() {
    return isset($this->meta['ownerId']);
  }


  public function setOwnerId($value) {
    $this->meta["ownerId"] = $value;
  }


  public function unsetOwnerId() {
    if ($this->isMetadataPresent('ownerId'))
      unset($this->meta['ownerId']);
  }


  public function getCreator() {
    return $this->meta['creator'];
  }


  public function issetCreator() {
    return isset($this->meta['creator']);
  }


  public function setCreator($value) {
    $this->meta['creator'] = $value;
  }


  public function unsetCreator() {
    if ($this->isMetadataPresent('creator'))
      unset($this->meta['creator']);
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