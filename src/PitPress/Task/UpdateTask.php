<?php

//! @file UpdateTaskhp
//! @brief
//! @details
//! @author Filippo F. Fadda


//! @brief This is the tasks namespace.
namespace PitPress\Task;


use Phalcon\CLI\Task;

use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Hook\UpdateScoreHook;


//! @brief This task updates the score of each post having new votes.
//! @nosubgrouping
class UpdateTask extends Task {

  protected $couch;


  public function initialize() {
    $this->couch = $this->getDI()['couchdb'];
  }


  //! @brief Updates all.
  public function mainAction() {
    $this->scoreAction();
  }


  //! @brief Updates the score.
  public function scoreAction() {
    $endKey = time() - 600; // Ten minutes ago.
    $hook = new UpdateScoreHook(); // The chunk hook.
    $hook->setDi($this->getDI());

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->includeDocs()->setEndKey($endKey);
    $this->couch->queryView("votes", "notRecorded", NULL, $opts, $hook);
  }

}