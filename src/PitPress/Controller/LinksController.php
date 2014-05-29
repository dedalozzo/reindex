<?php

//! @file LinksController.php
//! @brief Controller of Links actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;
use PitPress\Helper\Stat;


//! @brief Controller of Links actions.
//! @nosubgrouping
class LinksController extends ListController {


  public function afterExecuteRoute() {
    parent::afterExecuteRoute();

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getLinksCount());
  }


  //! @brief Displays the newest links.
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey(['links', new \stdClass()])->setEndKey(['links']);
    $rows = $this->couch->queryView("posts", "newestPerSection", NULL, $opts);

    $this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getLinksCount());
  }


  //! @brief Displays the most popular links.
  public function popularAction($period = 'settimana') {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  //! @brief Displays the last updated entries.
  public function updatedAction() {
  }


  //! @brief Displays the newest links based on my tags.
  public function interestingAction() {
  }


  public function rssAction() {
  }

}