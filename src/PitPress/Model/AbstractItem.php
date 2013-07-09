<?php

//! @file AbstractItem.php
//! @brief This file contains the AbstractItem class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


use ElephantOnCouch\Doc\Doc;
use ElephantOnCouch\Opt;

use Phalcon\DI;

use PitPress\Model\Accessory\Hit;


//! @brief This class is used to represent an abstract item.
//! @nosubgrouping
abstract class AbstractItem extends Doc {
  protected $di; // Stores the default Dependency Injector.
  protected $couch; // Stores the ElephantOnCouch client instance.


  public function __construct() {
    $this->di = DI::getDefault();
    $this->couch = $this->di['couchdb'];
  }


  public function getViewsCount() {
    $opts = new Opt\ViewQueryOpts();
    $opts->setKey($this->id);

    $this->couch->queryView("hits", "all", NULL, $opts);
  }


  public function incViews() {
    $hit = new Hit($this->id);

    $this->couch->saveDoc($hit);
  }



}