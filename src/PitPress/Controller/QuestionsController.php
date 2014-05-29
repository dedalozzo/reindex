<?php

//! @file QuestionsController.php
//! @brief This file contains the QuestionsController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;
use PitPress\Helper\Stat;


//! @brief Controller of Questions actions.
//! @nosubgrouping
class QuestionsController extends ListController {

  // Stores the still open answer sub-menu definition.
  protected static $stillOpenSubMenu = ['nessuna-risposta', 'popolari', 'nuove', 'rivolte-a-me'];


  public function afterExecuteRoute() {
    parent::afterExecuteRoute();

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getQuestionsCount());
  }


  //! @brief Displays the newest questions having a bounty.
  public function importantAction() {
  }


  //! @brief Displays the newest questions.
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey(['forum', new \stdClass()])->setEndKey(['forum']);
    $rows = $this->couch->queryView("posts", "newestPerSection", NULL, $opts);

    $this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getQuestionsCount());
  }


  //! @brief Displays the most popular questions.
  public function popularAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  //! @brief Displays the last updated questions.
  public function updatedAction() {
  }


  //! @brief Displays the newest questions based on user's tags.
  public function interestingAction() {
  }


  //! @brief Displays the questions, still open, based on user's tags.
  public function openAction($type) {
    if (empty($type))
      $type = 'rivolte-a-me';

    $this->view->setVar('subsectionMenu', self::$stillOpenSubMenu);
    $this->view->setVar('subsectionIndex', array_flip(self::$stillOpenSubMenu)[$type]);
  }


  public function rssAction() {
  }

}