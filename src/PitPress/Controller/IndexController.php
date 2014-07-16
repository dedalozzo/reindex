<?php

/**
 * @file IndexController.php
 * @brief This file contains the IndexController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;
use PitPress\Helper\Stat;

use Phalcon\Mvc\View;


/**
 * @brief Controller of Index actions.
 * @nosubgrouping
 */
class IndexController extends ListController {


  /*
   * @brief Gets the newest posts per type.
   */
  protected function getNewestPostsPerType($viewName, $type, $count = 20) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit($count)->reverseOrderOfResults()->setStartKey([$type, new \stdClass()])->setEndKey([$type]);
    $rows = $this->couch->queryView('posts', $viewName, NULL, $opts);

    // Entries.
    $keys = array_column($rows->asArray(), 'id');

    // Posts.
    $opts->reset();
    $opts->doNotReduce()->includeMissingKeys();
    $posts = $this->couch->queryView("posts", "all", $keys, $opts);

    // Scores.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $scores = $this->couch->queryView("votes", "perPost", $keys, $opts);

    // Replies.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $replies = $this->couch->queryView("replies", "perPost", $keys, $opts);

    $entries = [];
    $postCount = count($posts);
    for ($i = 0; $i < $postCount - 1; $i++) {
      $entry = new \stdClass();
      $entry->id = $posts[$i]['id'];

      $properties = $posts[$i]['value'];
      $entry->title = $properties['title'];
      $entry->url = $this->buildUrl($properties['section'], $properties['publishingDate'], $properties['slug']);
      $entry->whenHasBeenPublished = Time::when($properties['publishingDate']);
      $entry->score = is_null($scores[$i]['value']) ? 0 : $scores[$i]['value'];
      $entry->repliesCount = is_null($replies[$i]['value']) ? 0 : $replies[$i]['value'];

      $entries[] = $entry;
    }

    return $entries;
  }


  public function afterExecuteRoute() {
    parent::afterExecuteRoute();

    $this->view->setVar('articles', $this->getNewestPostsPerType('newestPerType', 'article'));
    $this->view->setVar('books', $this->getNewestPostsPerType('newestPerType', 'book'));

    $this->stats('getUpdatesCount', 'aggiornamenti');
  }


  /**
   * @brief Page index.
   */
  public function indexAction() {
    if (isset($this->user))
      return $this->dispatcher->forward(
        [
          'controller' => 'index',
          'action' => 'newest'
        ]);
    else
      return $this->dispatcher->forward(
        [
          'controller' => 'auth',
          'action' => 'signin'
        ]);
  }


  /**
   * @brief Displays the newest updates.
   */
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit(30);
    $rows = $this->couch->queryView("posts", "newest", NULL, $opts);

    $this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));
  }


  /**
   * @brief Displays the most popular updates for the provided period.
   */
  public function popularAction($period = '24-ore') {
    if (empty($period))
      $period = '24-ore';

    $this->view->setVar('subsectionMenu', Time::periods(6));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));

    $this->popularEver(new \stdClass(), $period);
  }


  /**
   * @brief Displays the last updated entries.
   */
  public function updatedAction() {
  }


  /**
   * @brief Displays the newest updates based on my tags.
   */
  public function interestingAction() {
  }


  /**
   * @brief Displays the tour page.
   */
  public function tourAction() {
    phpinfo();

    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Displays the help page.
   */
  public function helpAction() {

  }


  /**
   * @brief Displays a page with the legal info.
   */
  public function legalAction() {

  }


  /**
   * @brief Displays the privacy page.
   */
  public function privacyAction() {

  }


  /**
   * @brief Displays the career page.
   */
  public function careerAction() {

  }


  /**
   * @brief Displays the advertising page.
   */
  public function advertisingAction() {

  }


  /**
   * @brief Displays the contacts page.
   */
  public function contactAction() {

  }

}