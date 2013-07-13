<?php

//! @file Item.php@brief This file contains the Item class.
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


  //! @brief Constructor.
  public function __construct() {
    $this->di = DI::getDefault();
    $this->couch = $this->di['couchdb'];
  }


  //! @brief Returns the creation date.
  public function getCreationDate() {
    return $this->meta['creationDate'];
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