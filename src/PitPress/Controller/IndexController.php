<?php

//! @file IndexController.php
//! @brief This file contains the IndexController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;


//! @brief Controller of Index actions.
//! @nosubgrouping
class IndexController extends ListController {

  protected static $sectionLabel = 'AGGIORNAMENTI';

  // Stores the main menu definition.
  protected static $sectionMenu = [
    ['name' => 'interesting', 'path' => '/aggiornamenti/interessanti/', 'label' => 'INTERESSANTI', 'title' => 'Aggiornamenti interessanti'],
    ['name' => 'updated', 'path' => '/aggiornamenti/attivi/', 'label' => 'ATTIVI', 'title' => 'Contributi modificati'],
    ['name' => 'popular', 'path' => '/aggiornamenti/popolari/', 'label' => 'POPOLARI', 'title' => 'Aggiornamenti popolari'],
    ['name' => 'newest', 'path' => '/aggiornamenti/nuovi/', 'label' => 'NUOVI', 'title' => 'Ultimi aggiornamenti']
  ];


  public function initialize() {
    parent::initialize();

    $this->view->setVar('articles', $this->getLatestPostsPerType('latestPerType', 'article'));
    $this->view->setVar('books', $this->getLatestPostsPerType('latestPerType', 'book'));
    $this->view->setVar('tutorials', $this->getLatestPostsPerType('latestPerType', 'tutorial'));
  }


  //! @brief Displays the latest updates.
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults();
    $opts->setLimit(30);
    $rows = $this->couch->queryView("posts", "latest", NULL, $opts)['rows'];

    // Entries.
    $keys = array_column($rows, 'id');
    $this->view->entries = $this->getEntries($keys);
  }


  //! @brief Displays the most popular updates for the provided period.
  public function popularAction($period = '24-ore') {
    if (empty($period))
      $period = '24-ore';

    $this->view->setVar('subsectionMenu', Time::periods());
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));


    /*$opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults();
    $opts->setLimit(30);
    $rows = $this->couch->queryView("posts", "dailyPopular", NULL, $opts)['rows'];

    // Entries.
    $keys = array_column($rows, 'id');
    $this->view->entries = $this->getEntries($keys);*/
  }


  //! @brief Displays the last updated entries.
  public function updatedAction() {
  }


  //! @brief Displays the latest updates based on my tags.
  public function interestingAction() {
  }


  public function rssAction() {
  }

}