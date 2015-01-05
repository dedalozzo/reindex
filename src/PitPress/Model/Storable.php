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

use PitPress\Helper;


/**
 * @brief This class is used to represent a storable object.
 * @nosubgrouping
 */
abstract class Storable extends Doc {

  protected $di; // Stores the default Dependency Injector.
  protected $couch; // Stores the ElephantOnCouch client instance.
  protected $redis; // Stores the Redis client instance.

  protected $user; // Stores the current user.


  /**
   * @brief Constructor.
   */
  public function __construct() {
    $this->di = DI::getDefault();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
    $this->user = $this->di['guardian']->getUser();
  }


  /**
   * @brief Saves the item to the database.
   */
  public function save() {
    $this->modifiedAt = time();

    // Creation timestamp has not been provided.
    if (!isset($this->createdAt))
      $this->createdAt = $this->modifiedAt;

    $this->couch->saveDoc($this);
  }


  /**
   * @brief Returns `true` if the provided user id matches the current one, `false` otherwise.
   * @details This method is useful to check the ownership of a post, for example.
   * @param[in] string $id The id to match.
   * @raturn bool
   */
  public function match($id) {
    return ($this->id === $id) ? TRUE : FALSE;
  }


  /**
   * @brief Returns a measure of the time passed since the creation time. In case is passed more than a day, returns
   * a human readable date.
   * @return string
   */
  public function whenHasBeenCreated() {
    return Helper\Time::when($this->createdAt);
  }


  /**
   * @brief Returns a measure of the time passed since the last modification. In case is passed more than a day, returns
   * a human readable date.
   * @return string
   */
  public function whenHasBeenModified() {
    return Helper\Time::when($this->modifiedAt);
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