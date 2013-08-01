<?php

//! @file BlogController.php
//! @brief Controller of Blog actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;


//! @brief Controller of Blog actions.
//! @nosubgrouping
class BlogController extends BaseController {

  public function initialize() {
    \Phalcon\Tag::setTitle('Getting Help');
    parent::initialize();
  }

  public function latestAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce();
    $opts->reverseOrderOfResults();
    $opts->setLimit(30);

    $result = $this->couch->queryView("posts", "BY_type_ORDERBY_date", ['article, book, tutorial'], $opts)->getBodyAsArray();

    $posts = [];
    foreach ($result["rows"] as $row)
      $posts[] = $this->couch->getDoc(Couch::STD_DOC_PATH, $row["id"], NULL);

    $this->view->posts = $posts;
  }


  public function popularsAction() {
  }


  public function basedOnMyTagsAction() {
  }


  public function mostVotedAction() {
  }


  public function mostDiscussedAction() {
  }


  public function writtenByMeAction() {
  }


  public function rssAction() {
  }

}