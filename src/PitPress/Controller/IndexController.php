<?php

//! @file IndexController.php
//! @brief This file contains the IndexController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;
use PitPress\Helper\Stat;


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


  //! @brief Gets the newest posts per type.
  protected function getNewestPostsPerType($viewName, $type, $count = 20) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit($count)->reverseOrderOfResults()->setStartKey([$type, new \stdClass()])->setEndKey([$type]);
    $rows = $this->couch->queryView('posts', $viewName, NULL, $opts)['rows'];

    // Entries.
    $keys = array_column($rows, 'id');

    // Posts.
    $opts->reset();
    $opts->doNotReduce()->includeMissingKeys();
    $posts = $this->couch->queryView("posts", "all", $keys, $opts)['rows'];

    // Scores.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $scores = $this->couch->queryView("votes", "perPost", $keys, $opts)['rows'];

    $entries = [];
    $postCount = count($posts);
    for ($i = 0; $i < $postCount - 1; $i++) {
      $entry = new \stdClass();
      $entry->id = $posts[$i]['id'];

      $properties = &$posts[$i]['value'];
      $entry->title = $properties['title'];
      $entry->url = $properties['url'];
      $entry->whenHasBeenPublished = Time::when($properties['publishingDate']);
      $entry->score = is_null($scores[$i]['value']) ? 0 : $scores[$i]['value'];

      $entries[] = $entry;
    }

    return $entries;
  }


  public function afterExecuteRoute() {
    parent::afterExecuteRoute();

    $this->view->setVar('articles', $this->getNewestPostsPerType('newestPerType', 'article'));
    $this->view->setVar('books', $this->getNewestPostsPerType('newestPerType', 'book'));
    $this->view->setVar('tutorials', $this->getNewestPostsPerType('newestPerType', 'tutorial'));

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getUpdatesCount());
  }


  //! @brief Displays the newest updates.
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit(30);
    $rows = $this->couch->queryView("posts", "newest", NULL, $opts)['rows'];

    $this->view->setVar('entries', $this->getEntries(array_column($rows, 'id')));
  }


  //! @brief Displays the most popular updates for the provided period.
  public function popularAction($period = '24-ore') {
    if (empty($period))
      $period = '24-ore';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));

    $this->popularEver(new \stdClass(), $period);

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getBlogEntriesCount());
  }


  //! @brief Displays the last updated entries.
  public function updatedAction() {
  }


  //! @brief Displays the newest updates based on my tags.
  public function interestingAction() {
  }


  //! @brief Displays the tour page.
  public function tourAction() {

  }


  //! @brief Displays the help page.
  public function helpAction() {

  }


  //! @brief Displays a page with the legal info.
  public function legalAction() {

  }


  //! @brief Displays the privacy page.
  public function privacyAction() {

  }


  //! @brief Displays the career page.
  public function careerAction() {

  }


  //! @brief Displays the advertising page.
  public function advertisingAction() {

  }


  //! @brief Displays the contacts page.
  public function contactAction() {

  }

}