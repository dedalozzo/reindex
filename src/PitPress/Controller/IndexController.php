<?php

//! @file IndexController.php
//! @brief This file contains the IndexController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\DocOpts;
use ElephantOnCouch\Opt\ViewQueryOpts;


//! @brief Controller of Updates actions.
//! @nosubgrouping
class IndexController extends BaseController {

  public function initialize() {
    \Phalcon\Tag::setTitle('Getting Help');
    parent::initialize();
  }


  public function indexAction() {
    //$this->latestAction();
  }


  public function latestAction() {
    $queryOpts = new ViewQueryOpts();
    $queryOpts->doNotReduce();
    $queryOpts->reverseOrderOfResults();
    $queryOpts->setLimit(30);

    $results = $this->couch->queryView("index", "latest", $queryOpts)->getBodyAsArray();

    $items = [];
    foreach ($results["rows"] as $row) {
      $items[] = $this->couch->getDoc(Couch::STD_DOC_PATH, $row["id"], NULL, $docOpts);
    }

    $this->view->setVar("items", $items);
  }


  public function popularAction() {
  }


  public function basedOnMyTagsAction() {
  }


  public function mostVotedAction() {
  }


  public function mostDiscussedAction() {
  }


  public function rssAction() {
  }

}