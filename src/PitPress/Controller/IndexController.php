<?php

//! @file IndexController.php
//! @brief Controller of Updates actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\DocOpts;
use ElephantOnCouch\ElephantOnCouch;
use ElephantOnCouch\ViewQueryArgs;


class IndexController extends BaseController {

  public function initialize() {
    \Phalcon\Tag::setTitle('Getting Help');
    parent::initialize();
  }


  public function indexAction() {
    $queryArgs = new ViewQueryArgs();

    $queryArgs->setLimit(20);
    $queryArgs->doNotReduce();
    //$queryArgs->includeDocs();
    $results = $this->couch->queryView("articles", "articles_by_id", $queryArgs)->getBodyAsArray();

    $docOpts = new DocOpts();
    $docOpts->ignoreClassName = TRUE;

    $items = [];
    foreach ($results["rows"] as $row) {
      $items[] = $this->couch->getDoc(ElephantOnCouch::STD_DOC_PATH, $row["id"], NULL, $docOpts);
    }

    $this->view->setVar("items", $items);
  }


  public function recentsAction() {
  }


  public function popularsAction() {
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