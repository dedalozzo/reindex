<?php

//! @file IndexController.php
//! @brief This file contains the IndexController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;


//! @brief Controller of Index actions.
//! @nosubgrouping
class IndexController extends ListController {

  protected static $controllerPath = '/';
  protected static $controllerIndex = 0;
  protected static $controllerLabel = 'AGGIORNAMENTI';

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


  //! @brief Displays the latest updates.
  public function newestAction() {
    //$this->initAction('Ultimi aggiornamenti', 3);

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults();
    $opts->setLimit(30);
    $rows = $this->couch->queryView("posts", "latest", NULL, $opts)['rows'];

    // Entries.
    $keys = array_column($rows, 'id');
    $this->view->entries = $this->getEntries($keys);
  }


  //! @brief Displays the most popular updates of today.
  public function popularAction() {
    $this->initAction('Aggiornamenti popolari', 2, '', self::$periodSubMenu, 6);

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults();
    $opts->setLimit(30);
    $rows = $this->couch->queryView("posts", "dailyPopular", NULL, $opts)['rows'];

    // Entries.
    $keys = array_column($rows, 'id');
    $this->view->entries = $this->getEntries($keys);
  }


  //! @brief Displays the last updated entries.
  public function updatedAction() {
    //$this->initAction('Contributi modificati', 1);
  }


  //! @brief Displays the latest updates based on my tags.
  public function interestingAction() {
    //$this->initAction('Aggiornamenti interessanti', 1);
  }


  public function rssAction() {
  }

}