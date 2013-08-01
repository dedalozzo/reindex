<?php

//! @file Item.php
//! @brief This file contains the Item class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


use ElephantOnCouch\Doc\Doc;

use Phalcon\DI;


//! @brief This class is used to represent an abstract item.
//! @nosubgrouping
abstract class Item extends Doc {
  protected $di; // Stores the default Dependency Injector.
  protected $couch; // Stores the ElephantOnCouch client instance.
  protected $redis; // Stores the Redis client instance.


  //! @brief Constructor.
  public function __construct() {
    $this->di = DI::getDefault();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
  }


  //! @name Hits Counting Methods
  // @{

  //! @brief Returns the times the item has been viewed.
  public function getHitsCount() {
    if (isset($this->rev))
      return $this->redis->hGet($this->id, 'hits');
    else
      return 0;
  }


  //! @brief Increments the times the item has been viewed.
  public function incHits() {
    // We can increment the views of a document that has been already saved.
    if (isset($this->rev))
      $this->redis->hIncrBy($this->id, 'hits', 1);
  }

  //@}


  //! @brief Returns the creation timestamp.
  public function getTimestamp() {
    return $this->meta['timestamp'];
  }


  //! @brief Returns information about the last update.
  public function getLastUpdateInfo() {

  }


  //! @brief Saves the item to the database.
  public function save() {
    $this->meta['lastUpdate'] = time();
    $this->couch->saveDoc($this);
  }

}