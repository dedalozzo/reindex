<?php

/**
 * @file BlogController.php
 * @brief Controller of Blog actions.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;

use Phalcon\Mvc\View;


/**
 * @brief Controller of Blog actions.
 * @nosubgrouping
 * @bug
 */
class BlogController extends ListController {


  protected function newestInPeriod($type, $period) {
    $opts = new ViewQueryOpts();

    if ($period != 'sempre')
      $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$type, time()])->setEndKey([$type, Time::timestamp($period)]);
    else
      $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$type, new \stdClass()])->setEndKey([$type]);

    $rows = $this->couch->queryView('posts', 'newestPerType', NULL, $opts);

    $this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));
    $this->stats('getBlogEntriesCount', 'pubblicazioni');
  }


  /**
   * @brief Displays the blog entries per date.
   */
  public function perDateAction($year, $month, $day) {
    $this->perDate('blog', $year, $month, $day);
    $this->view->setVar('title', 'Pubblicazioni per data');
    $this->stats('getBlogEntriesCount', 'pubblicazioni');
  }


  /**
   * @brief Displays the blog post.
   */
  public function showAction($year, $month, $day, $slug) {
    $opts = new ViewQueryOpts();
    $opts->setKey(['blog', $year, $month, $day, $slug])->setLimit(1);
    $rows = $this->couch->queryView("posts", "byUrl", NULL, $opts);

    if ($rows->isEmpty())
      return $this->dispatcher->forward(
        [
          'controller' => 'error',
          'action' => 'show404'
        ]);

    $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $rows[0]['id']);
    $doc->incHits();
    $doc->html = $this->markdown->parse($doc->body);

    $this->view->setVar('doc', $doc);
    $this->view->setVar('replies', $doc->getReplies());
    $this->view->setVar('title', $doc->title);

    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  public function editAction($id) {
    if (empty($id))
      return $this->dispatcher->forward(
        [
          'controller' => 'error',
          'action' => 'show404'
        ]);

    $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $id);

    $this->tag->setDefault("title", $doc->title);
    $this->tag->setDefault("body", $doc->body);

    $this->view->setVar('doc', $doc);
    $this->view->setVar('title', $doc->title);

    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Displays the newest blog entries.
   */
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey(['blog', new \stdClass()])->setEndKey(['blog']);
    $rows = $this->couch->queryView("posts", "newestPerSection", NULL, $opts);

    $this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));
    $this->stats('getBlogEntriesCount', 'pubblicazioni');
  }


  /**
   * @brief Displays the most popular blog entries.
   */
  public function popularAction($period = "settimana") {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));

    $this->popularEver('blog');
    $this->stats('getBlogEntriesCount', 'pubblicazioni');
  }


  /**
   * @brief Displays the last updated blog entries.
   */
  public function updatedAction() {
    //$this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));
    $this->stats('getBlogEntriesCount', 'pubblicazioni');
  }


  /**
   * @brief Displays the newest blog entries based on my tags.
   */
  public function interestingAction() {
    //$this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));
    $this->stats('getBlogEntriesCount', 'pubblicazioni');
  }


  /**
   * @brief Displays the newest articles.
   */
  public function articlesAction($period) {
    if (empty($period))
      $period = 'trimestre';

    $this->view->setVar('subsectionMenu', Time::periods(4));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));

    $this->newestInPeriod('article', $period);
    $this->stats('getArticlesCount', 'articoli');
  }


  /**
   * @brief Displays the newest books.
   */
  public function booksAction($period) {
    if (empty($period))
      $period = 'trimestre';

    $this->view->setVar('subsectionMenu', Time::periods(4));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));

    $this->newestInPeriod('book', $period);
    $this->stats('getBooksCount', 'libri');
  }


  /**
   * @brief Displays the rss of the newest blog entries.
   */
  public function rssAction() {
  }

}