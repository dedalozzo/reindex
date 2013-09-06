<?php

//! @file BlogController.php
//! @brief Controller of Blog actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;


//! @brief Controller of Blog actions.
//! @nosubgrouping
class BlogController extends ListController {

  protected static $sectionLabel = 'PUBBLICAZIONI';

  // Stores the main menu definition.
  protected static $sectionMenu = [
    ['name' => 'books', 'link' => 'libri/', 'label' => 'LIBRI', 'title' => 'Libri'],
    ['name' => 'tutorials', 'link' => 'guide/', 'label' => 'GUIDE', 'title' => 'Guide'],
    ['name' => 'articles', 'link' => 'articoli/', 'label' => 'ARTICOLI', 'title' => 'Articoli'],
    ['name' => 'interesting', 'link' => 'interessanti/', 'label' => 'INTERESSANTI', 'title' => 'Pubblicazioni interessanti'],
    ['name' => 'updated', 'link' => 'aggiornati/', 'label' => 'AGGIORNATE', 'title' => 'Pubblicazioni modificate di recente'],
    ['name' => 'popular', 'link' => 'popolari/', 'label' => 'POPOLARI', 'title' => 'Pubblicazioni popolari'],
    ['name' => 'newest', 'link' => 'nuovi/', 'label' => 'NUOVE', 'title' => 'Ultime pubblicazioni']
  ];


  //! @brief Displays the latest blog entries.
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey(['blog', new \stdClass()])->setEndKey(['blog']);
    $rows = $this->couch->queryView("posts", "latestPerSection", NULL, $opts)['rows'];

    // Entries.
    $keys = array_column($rows, 'id');
    $this->view->entries = $this->getEntries($keys);
  }


  //! @brief Displays the most popular blog entries.
  public function popularAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  //! @brief Displays the last updated blog entries.
  public function updatedAction() {
  }


  //! @brief Displays the latest blog entries based on my tags.
  public function interestingAction() {
  }


  //! @brief Displays the latest articles.
  public function articlesAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  //! @brief Displays the latest tutorials.
  public function tutorialsAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  //! @brief Displays the latest books.
  public function booksAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  //! @brief Displays the rss of the latest blog entries.
  public function rssAction() {
  }

}