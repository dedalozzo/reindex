<?php

//! @file IndexController.php
//! @brief This file contains the IndexController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Stat;


//! @brief Controller of Index actions.
//! @nosubgrouping
class IndexController extends BaseController {


  protected function getRecentTags() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(60);
    $result = $this->couch->queryView("classifications", "allLatest", NULL, $opts)->getBodyAsArray();

    $keys = [];
    foreach ($result['rows'] as $classification)
      $keys[] = $classification['value'];

    $opts->reset();
    $opts->doNotReduce();
    $tags = $this->couch->queryView("tags", "all", $keys, $opts)->getBodyAsArray();

    $opts->reset();
    $opts->groupResults();
    $postsPerTag = $this->couch->queryView("classifications", "perTag", $keys, $opts)->getBodyAsArray();

    $recentTags = [];
    for ($i = 0; $i < 60; $i++)
      $recentTags[] = [$tags['rows'][$i]['value'], $postsPerTag['rows'][$i]['value']];

    return $recentTags;
  }


  protected function getRecentArticles() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30);
    $result = $this->couch->queryView("articles", "allLatest", NULL, $opts)->getBodyAsArray();

    $keys = [];
    foreach ($result['rows'] as $classification)
      $keys[] = $classification['value'];

    $opts->reset();
    $opts->doNotReduce();
    $tags = $this->couch->queryView("tags", "all", $keys, $opts)->getBodyAsArray();

    $opts->reset();
    $opts->groupResults();
    $postsPerTag = $this->couch->queryView("classifications", "perTag", $keys, $opts)->getBodyAsArray();

    $recentTags = [];
    for ($i = 0; $i < 60; $i++)
      $recentTags[] = [$tags['rows'][$i]['value'], $postsPerTag['rows'][$i]['value']];

    return $recentTags;
  }


  public function initialize() {
    \Phalcon\Tag::setTitle('Getting Help');
    parent::initialize();
  }


  public function indexAction() {
    $this->latestAction();
  }


  public function notFoundAction() {
    $this->response->setHeader(404 , 'Not Found');
    $this->view->pick('404/404');
  }


  public function latestAction() {
    // Posts.
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults();
    $opts->setLimit(30);
    $result = $this->couch->queryView("posts", "allLatest", NULL, $opts)->getBodyAsArray();

    $posts = [];
    foreach ($result["rows"] as $row) {
      $doc = $this->couch->getDoc(Couch::STD_DOC_PATH, $row["id"], NULL);
      $doc->body = $this->markdown->render($doc->body);
      $posts[] = $doc;
    }

    $this->view->posts = $posts;

    // Stats.
    $this->view->stat = new Stat();

    // Recent tags.
    $this->view->recentTags = $this->getRecentTags();
  }


  public function popularAction() {
    // Retrieves the post of the last 7 days.
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults();

    $aWeekAgo = (string)strtotime("-1 week");
    $opts->setStartKey($aWeekAgo);

    $result = $this->couch->queryView("index", "latest", $opts)->getBodyAsArray();

    $posts = [];
    /*foreach ($result["rows"] as $row)
      $this->redis->hGet($row->id)


    $posts[] = $this->couch->getDoc(Couch::STD_DOC_PATH, $row["id"], NULL, $docOpts);

    $this->view->setVar("posts", $posts);*/
  }


  public function basedOnMyTagsAction() {
  }


  public function mostVotedAction() {
    // Posts.
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults();
    $opts->setLimit(30);

    $result = $this->couch->queryView("votes", "allMostVoted", NULL, $opts)->getBodyAsArray();

    $posts = [];
    foreach ($result["rows"] as $row)
      $posts[] = $this->couch->getDoc(Couch::STD_DOC_PATH, $row["id"], NULL);

    $this->view->posts = $posts;

    // Stats.
    $this->view->stat = new Stat();

    // Recent tags.
    $this->view->recentTags = $this->getRecentTags();
  }


  public function mostDiscussedAction() {
  }


  public function rssAction() {
  }

}