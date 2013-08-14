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
    \Phalcon\Tag::setTitle('Blog');
    parent::initialize();
  }


  //! @brief Gets the latest blog entries.
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


  //! @brief Gets the most popular weekly blog entries.
  public function popularsAction() {
  }


  //! @brief Gets the last weekly blog entries based on my tags.
  public function basedOnMyTagsAction() {
  }


  //! @brief Gets the most voted blog entries.
  public function mostVotedAction() {
  }


  //! @brief Gets the most discussed blog entries.
  public function mostDiscussedAction() {
  }


  //! @brief Gets my blog entries.
  public function writtenByMeAction() {
  }


  //! @brief Gets the rss of the latest blog entries.
  public function rssAction() {
  }

}