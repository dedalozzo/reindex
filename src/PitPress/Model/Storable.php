<?php

/**
 * @file Storable.php
 * @brief This file contains the Storable class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model;


use ElephantOnCouch\Doc\Doc;

use Phalcon\DI;


/**
 * @brief This class is used to represent a storable object.
 * @nosubgrouping
 */
abstract class Storable extends Doc {

  protected $di; // Stores the default Dependency Injector.
  protected $couch; // Stores the ElephantOnCouch client instance.
  protected $redis; // Stores the Redis client instance.


  /**
   * @brief Constructor.
   */
  public function __construct() {
    $this->di = DI::getDefault();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
  }


  /**
   * @brief Saves the item to the database.
   */
  public function save() {
    $this->modifiedAt = time();

    // Creation timestamp has not been provided.
    if (is_null($this->createdAt))
      $this->createdAt = $this->modifiedAt;

    $this->couch->saveDoc($this);
  }


  //! @cond HIDDEN_SYMBOLS

  public function getCreatedAt() {
    return $this->meta['createdAt'];
  }


  public function issetCreatedAt() {
    return isset($this->meta['createdAt']);
  }


  public function setCreatedAt($value) {
    $this->meta['createdAt'] = $value;
  }


  public function unsetCreatedAt() {
    if ($this->isMetadataPresent('createdAt'))
      unset($this->meta['createdAt']);
  }


  public function getModifiedAt() {
    return $this->meta['modifiedAt'];
  }


  public function issetModifiedAt() {
    return isset($this->meta['modifiedAt']);
  }


  public function setModifiedAt($value) {
    $this->meta['modifiedAt'] = $value;
  }


  public function unsetModifiedAt() {
    if ($this->isMetadataPresent('modifiedAt'))
      unset($this->meta['modifiedAt']);
  }

  //! @endcond

}