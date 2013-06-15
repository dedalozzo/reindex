<?php

//! @file AbstractItem.php
//! @brief This file contains the AbstractItem class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


use ElephantOnCouch\Doc\Doc;
use ElephantOnCouch\ElephantOnCouch;
use Phalcon\DI;
use PitPress\Model\Accessory\Display;


//! @brief This class is used to represent an abstract item.
//! @nosubgrouping
abstract class AbstractItem extends Doc {
  protected $di; // Stores the default Dependency Injector.
  protected $couch; // Stores the ElephantOnCouch client instance.


  public function __construct() {
    $this->di = DI::getDefault();
    $this->couch = $this->di['couchdb'];
  }


  public function getDisplaysCount() {
    $this->couch->queryView("general", "displays");
  }


  public function incDisplays() {
    $display = new Display($this->id);

    $this->couch->saveDoc($display);
  }



}