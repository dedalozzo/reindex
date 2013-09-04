<?php

//! @file LinksController.php
//! @brief Controller of Links actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;


//! @brief Controller of Links actions.
//! @nosubgrouping
class LinksController extends ListController {

  protected static $controllerPath = '/links/';
  protected static $controllerIndex = 3;
  protected static $controllerLabel = 'LINKS';

  // Stores the main menu definition.
  protected static $actionMenu = [
    ['link' => 'interessanti/', 'name' => 'INTERESSANTI'],
    ['link' => 'attivi/', 'name' => 'ATTIVI'],
    ['link' => 'popolari/', 'name' => 'POPOLARI'],
    ['link' => 'nuovi/', 'name' => 'NUOVI']
  ];

  // Stores the popular sub-menu definition.
  protected static $periodSubMenu = [
    ['link' => 'sempre/', 'name' => 'SEMPRE'],
    ['link' => 'anno/', 'name' => 'ANNO'],
    ['link' => 'trimestre/', 'name' => 'TRIMESTRE'],
    ['link' => 'mese/', 'name' => 'MESE'],
    ['link' => 'settimana/', 'name' => 'SETTIMANA'],
    ['link' => 'ieri/', 'name' => 'IERI'],
    ['link' => 'oggi/', 'name' => 'OGGI']
  ];


  //! Displays the index.
  public function indexAction() {
    $this->newestAction();
  }


  //! @brief Displays the latest links.
  public function newestAction() {
    $this->view->sectionIndex = 3;
    $this->view->title = "Nuovi links";

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey(['link', new \stdClass()])->setEndKey(['link']);
    $rows = $this->couch->queryView("posts", "latestPerType", NULL, $opts)['rows'];

    // Entries.
    $keys = array_column($rows, 'id');
    $this->view->entries = $this->getEntries($keys);
  }


  //! @brief Displays the most popular links of today.
  public function todayPopularAction() {
    $this->view->sectionIndex = 2;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 6;
    $this->view->title = "Links popolari";
  }


  //! @brief Displays the most popular links of yesterday.
  public function yesterdayPopularAction() {
    $this->view->sectionIndex = 2;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 5;
    $this->view->title = "Links popolari";
  }


  //! @brief Displays the most popular weekly links.
  public function weeklyPopularAction() {
    $this->view->sectionIndex = 2;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 4;
    $this->view->title = "Links popolari";
  }


  //! @brief Displays the most popular monthly links.
  public function monthlyPopularAction() {
    $this->view->sectionIndex = 2;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 3;
    $this->view->title = "Links popolari";
  }


  //! @brief Displays the most popular quarterly links.
  public function quarterlyPopularAction() {
    $this->view->sectionIndex = 2;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 2;
    $this->view->title = "Links popolari";
  }


  //! @brief Displays the most popular yearly links.
  public function yearlyPopularAction() {
    $this->view->sectionIndex = 2;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 1;
    $this->view->title = "Links popolari";
  }


  //! @brief Displays the most popular ever links.
  public function everPopularAction() {
    $this->view->sectionIndex = 2;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 0;
    $this->view->title = "Links popolari";
  }


  //! @brief Displays the last updated entries.
  public function updatedAction() {
    $this->view->sectionIndex = 1;
    $this->view->title = "Links aggiornati di recente";
  }


  //! @brief Displays the latest links based on my tags.
  public function interestingAction() {
    $this->view->sectionIndex = 0;
    $this->view->title = "Links interessanti";
  }


  public function rssAction() {
  }

}