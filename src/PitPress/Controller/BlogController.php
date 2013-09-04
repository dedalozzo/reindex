<?php

//! @file BlogController.php
//! @brief Controller of Blog actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Stat;


//! @brief Controller of Blog actions.
//! @nosubgrouping
class BlogController extends ListController {

  protected static $controllerPath = '/blog/';
  protected static $controllerIndex = 1;
  protected static $controllerLabel = 'PUBBLICAZIONI';

  // Stores the main menu definition.
  protected static $actionMenu = [
    ['link' => 'articoli/', 'name' => 'PER TIPOLOGIA'],
    ['link' => 'interessanti/', 'name' => 'INTERESSANTI'],
    ['link' => 'aggiornati/', 'name' => 'AGGIORNATE'],
    ['link' => 'popolari/', 'name' => 'POPOLARI'],
    ['link' => 'nuovi/', 'name' => 'NUOVE']
  ];

  // Stores the popular sub-menu definition.
  protected static $periodSubMenu = [
    ['link' => 'sempre/', 'name' => 'SEMPRE'],
    ['link' => 'anno/', 'name' => 'ANNO'],
    ['link' => 'trimestre/', 'name' => 'TRIMESTRE'],
    ['link' => 'mese/', 'name' => 'MESE'],
    ['link' => 'settimana/', 'name' => 'SETTIMANA']
  ];

  // Stores the popular sub-menu definition.
  protected static $typologySubMenu = [
    ['link' => 'libri/', 'name' => 'LIBRI'],
    ['link' => 'guide/', 'name' => 'GUIDE'],
    ['link' => 'articoli/', 'name' => 'ARTICOLI']
  ];


  //! Displays the index.
  public function indexAction() {
    $this->newestAction();
  }


  //! @brief Displays the latest blog entries.
  public function newestAction() {
    $this->view->sectionIndex = 4;
    $this->view->title = "Ultime pubblicazioni";

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey(['blog', new \stdClass()])->setEndKey(['blog']);
    $rows = $this->couch->queryView("posts", "latestPerSection", NULL, $opts)['rows'];

    // Entries.
    $keys = array_column($rows, 'id');
    $this->view->entries = $this->getEntries($keys);
  }


  //! @brief Displays the most popular weekly blog entries.
  public function weeklyPopularAction() {
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 4;
    $this->view->title = "Pubblicazioni popolari";
  }


  //! @brief Displays the most popular monthly blog entries.
  public function monthlyPopularAction() {
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 3;
    $this->view->title = "Pubblicazioni popolari";
  }


  //! @brief Displays the most popular quarterly blog entries.
  public function quarterlyPopularAction() {
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 2;
    $this->view->title = "Pubblicazioni popolari";
  }


  //! @brief Displays the most popular yearly blog entries.
  public function yearlyPopularAction() {
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 1;
    $this->view->title = "Pubblicazioni popolari";
  }


  //! @brief Displays the most popular ever blog entries.
  public function everPopularAction() {
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 0;
    $this->view->title = "Pubblicazioni popolari";
  }

  //! @brief Displays the last updated blog entries.
  public function updatedAction() {
    $this->view->sectionIndex = 2;
    $this->view->title = "Pubblicazioni modificate di recente";
  }


  //! @brief Displays the latest blog entries based on my tags.
  public function interestingAction() {
    $this->view->sectionIndex = 1;
    $this->view->title = "Pubblicazioni interessanti";
  }


  //! @brief Displays the latest articles.
  public function articlesAction() {
    $this->view->sectionIndex = 0;
    $this->view->subMenu = self::$typologySubMenu;
    $this->view->subIndex = 2;
    $this->view->title = "Articoli";
  }


  //! @brief Displays the latest tutorials.
  public function tutorialsAction() {
    $this->view->sectionIndex = 0;
    $this->view->subMenu = self::$typologySubMenu;
    $this->view->subIndex = 1;
    $this->view->title = "Guide";
  }


  //! @brief Displays the latest books.
  public function booksAction() {
    $this->view->sectionIndex = 0;
    $this->view->subMenu = self::$typologySubMenu;
    $this->view->subIndex = 0;
    $this->view->title = "Recensioni di testi tecnici";
  }


  //! @brief Displays the rss of the latest blog entries.
  public function rssAction() {
  }

}