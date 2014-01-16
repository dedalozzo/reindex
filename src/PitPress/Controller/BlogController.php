<?php

//! @file BlogController.php
//! @brief Controller of Blog actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;
use PitPress\Helper\Stat;

use Phalcon\Mvc\View;


//! @brief Controller of Blog actions.
//! @nosubgrouping
//! @bug
class BlogController extends ListController {

  protected static $sectionLabel = 'PUBBLICAZIONI';

  // Stores the main menu definition.
  protected static $sectionMenu = [
    ['name' => 'books', 'path' => '/libri/', 'label' => 'LIBRI', 'title' => 'Libri'],
    ['name' => 'tutorials', 'path' => '/guide/', 'label' => 'GUIDE', 'title' => 'Guide'],
    ['name' => 'articles', 'path' => '/articoli/', 'label' => 'ARTICOLI', 'title' => 'Articoli'],
    ['name' => 'interesting', 'path' => '/pubblicazioni/interessanti/', 'label' => 'INTERESSANTI', 'title' => 'Pubblicazioni interessanti'],
    ['name' => 'updated', 'path' => '/pubblicazioni/aggiornate/', 'label' => 'AGGIORNATE', 'title' => 'Pubblicazioni modificate di recente'],
    ['name' => 'popular', 'path' => '/pubblicazioni/popolari/', 'label' => 'POPOLARI', 'title' => 'Pubblicazioni popolari'],
    ['name' => 'newest', 'path' => '/pubblicazioni/nuove/', 'label' => 'NUOVE', 'title' => 'Ultime pubblicazioni']
  ];


  private function newestInPeriod($type, $period) {
    $opts = new ViewQueryOpts();

    if ($period != 'sempre')
      $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$type, time()])->setEndKey([$type, Time::timestamp($period)]);
    else
      $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$type, new \stdClass()])->setEndKey([$type]);

    $rows = $this->couch->queryView('posts', 'newestPerType', NULL, $opts)['rows'];

    $this->view->setVar('entries', $this->getEntries(array_column($rows, 'id')));
  }


  public function showAction($year, $month, $day, $slug) {
    $opts = new ViewQueryOpts();
    $opts->setKey(['blog', $year, $month, $day, $slug])->setLimit(1);
    $rows = $this->couch->queryView("posts", "byUrl", NULL, $opts)['rows'];

    if (empty($rows))
      $this->dispatcher->forward(
        [
          'controller' => 'error',
          'action' => 'show404'
        ]);

    $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $rows[0]['id']);
    $doc->incHits();
    $this->view->setVar('doc', $doc);
    $this->view->setVar('replays', $doc->getReplays());

    $this->view->setVar('title', $doc->title);

    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  //! @brief Displays the newest blog entries.
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey(['blog', new \stdClass()])->setEndKey(['blog']);
    $rows = $this->couch->queryView("posts", "newestPerSection", NULL, $opts)['rows'];

    $this->view->setVar('entries', $this->getEntries(array_column($rows, 'id')));

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getBlogEntriesCount());
  }


  //! @brief Displays the most popular blog entries.
  public function popularAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));

    $this->popularEver('blog');

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getBlogEntriesCount());
  }


  //! @brief Displays the last updated blog entries.
  public function updatedAction() {
    //$this->view->setVar('entries', $this->getEntries(array_column($rows, 'id')));

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getBlogEntriesCount());
  }


  //! @brief Displays the newest blog entries based on my tags.
  public function interestingAction() {
    //$this->view->setVar('entries', $this->getEntries(array_column($rows, 'id')));

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getBlogEntriesCount());
  }


  //! @brief Displays the newest articles.
  public function articlesAction($period) {
    if (empty($period))
      $period = 'trimestre';

    $this->view->setVar('subsectionMenu', Time::periods(4));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));

    $this->newestInPeriod('article', $period);

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getArticlesCount());
  }


  //! @brief Displays the newest tutorials.
  public function tutorialsAction($period) {
    if (empty($period))
      $period = 'sempre';

    $this->view->setVar('subsectionMenu', Time::periods(3));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));

    $this->newestInPeriod('tutorial', $period);

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getTutorialsCount());
  }


  //! @brief Displays the newest books.
  public function booksAction($period) {
    if (empty($period))
      $period = 'trimestre';

    $this->view->setVar('subsectionMenu', Time::periods(4));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));

    $this->newestInPeriod('book', $period);

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getBooksCount());
  }


  //! @brief Displays the rss of the newest blog entries.
  public function rssAction() {
  }

}