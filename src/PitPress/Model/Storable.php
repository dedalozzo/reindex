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
 * @details A storable object is not versionable, but for implementation reasons provides the ability to get the ID
 * pruned of its version number. This happens because all the extensions work with an unversion ID.
 * @nosubgrouping
 */
abstract class Storable extends Doc {

  const SEPARATOR = '::'; //!< Used to separate the ID from the version number.

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
   * @brief Prunes the ID of its version number, if any.
   * @return string
   */
  public function getUnversionId() {
    return strtok($this->meta['_id'], self::SEPARATOR);
  }


  /**
   * @brief Returns information about the last update.
   */
  public function getLastUpdateInfo() {
    // todo
  }

  /**
   * @brief Saves the item to the database.
   */
  public function save() {
    $this->meta['lastUpdate'] = time();
    $this->couch->saveDoc($this);
  }

}