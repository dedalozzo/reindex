<?php

//! @file DatabaseTask.php
//! @brief
//! @details
//! @author Filippo F. Fadda


//! @brief
namespace PitPress\Task;


use Phalcon\CLI\Task;
use ElephantOnCouch\Couch;


//! @brief
//! @nosubgrouping
class DatabaseTask extends Task {

  protected $couch;


  public function initialize() {
  }


  // Default action.
  public function mainAction() {
    // todo show the help

    echo "this show the help";
  }


  public function createAction() {
    $config = $this->_dependencyInjector;

    $couch = new ElephantOnCouch(ElephantOnCouch::DEFAULT_SERVER, $config->couchdb->user, $config->couchdb->password);
    $couch->useCurl();
    $couch->createDb($config->couchdb->database);
  }


  public function commitAction() {
    $this->couch = $this->_dependencyInjector['couchdb'];

    $this->couch->compactDb();
  }

}