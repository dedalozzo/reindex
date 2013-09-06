<?php

//! @file LinksController.php
//! @brief Controller of Links actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;


//! @brief Controller of Links actions.
//! @nosubgrouping
class LinksController extends ListController {

  protected static $sectionLabel = 'LINKS';

  // Stores the main menu definition.
  protected static $sectionMenu = [
    ['name' => 'interesting', 'link' => 'interessanti/', 'label' => 'INTERESSANTI', 'title' => 'Links interessanti'],
    ['name' => 'updated', 'link' => 'aggiornati/', 'label' => 'AGGIORNATI', 'title' => 'Links aggiornati di recente'],
    ['name' => 'popular', 'link' => 'popolari/', 'label' => 'POPOLARI', 'title' => 'Links popolati'],
    ['name' => 'newest', 'link' => 'nuovi/', 'label' => 'NUOVI', 'title' => 'Links interessanti']
  ];


  //! @brief Displays the latest links.
  public function newestAction() {
    $this->view->sectionIndex = 3;
    $this->view->title = "Nuovi links";

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey(['link', new \stdClass()])->setEndKey(['link']);
    $rows = $this->couch->queryView("posts", "latestPerType", NULL, $opts)['rows'];

    // Entries.
    $keys = array_column($rows, 'id');
    $this->view->setVar('entries', $this->getEntries($keys));
  }


  //! @brief Displays the most popular links.
  public function popularAction() {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods());
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  //! @brief Displays the last updated entries.
  public function updatedAction() {
  }


  //! @brief Displays the latest links based on my tags.
  public function interestingAction() {
  }


  public function rssAction() {
  }

}