<?php

//! @file ForumController.php
//! @brief Controller of Forum actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;


//! @brief Controller of Forum actions.
//! @nosubgrouping
class ForumController extends ListController {

  protected static $controllerPath = '/domande/';
  protected static $controllerIndex = 2;
  protected static $controllerLabel = 'DOMANDE';

  // Stores the main menu definition.
  protected static $actionMenu = [
    ['link' => 'senza-risposta/', 'name' => 'SENZA RISPOSTA'],
    ['link' => 'interessanti/', 'name' => 'INTERESSANTI'],
    ['link' => 'aggiornate/', 'name' => 'AGGIORNATE'],
    ['link' => 'popolari/', 'name' => 'POPOLARI'],
    ['link' => 'nuove/', 'name' => 'NUOVE'],
    ['link' => 'importanti/', 'name' => 'IMPORTANTI']
  ];

  // Stores the popular sub-menu definition.
  protected static $periodSubMenu = [
    ['link' => 'sempre/', 'name' => 'SEMPRE'],
    ['link' => 'anno/', 'name' => 'ANNO'],
    ['link' => 'trimestre/', 'name' => 'TRIMESTRE'],
    ['link' => 'mese/', 'name' => 'MESE'],
    ['link' => 'settimana/', 'name' => 'SETTIMANA']
  ];

  // Stores the still open answer sub-menu definition.
  protected static $stillOpenSubMenu = [
    ['link' => 'nessuna-risposta/', 'name' => 'NESSUNA RISPOSTA'],
    ['link' => 'popolari/', 'name' => 'POPOLARI'],
    ['link' => 'nuove/', 'name' => 'NUOVE'],
    ['link' => 'rivolte-a-me/', 'name' => 'RIVOLTE A ME']
  ];


  //! Displays the index.
  public function indexAction() {
    $this->newestAction();
  }


  //! @brief Displays the latest questions having a bounty.
  public function importantAction() {
    $this->view->sectionIndex = 5;
    $this->view->title = "Domande con un premio attivo";
  }


  //! @brief Displays the latest questions.
  public function newestAction() {
    $this->view->sectionIndex = 4;
    $this->view->title = "Ultime domande";

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey(['question', new \stdClass()])->setEndKey(['question']);
    $rows = $this->couch->queryView("posts", "latestPerType", NULL, $opts)['rows'];

    // Entries.
    $keys = array_column($rows, 'id');
    $this->view->entries = $this->getEntries($keys);
  }


  //! @brief Displays the most popular weekly questions.
  public function weeklyPopularAction() {
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 0;
    $this->view->title = "Domande popolari";
  }


  //! @brief Displays the most popular monthly questions.
  public function monthlyPopularAction() {
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 1;
    $this->view->title = "Domande popolari";
  }


  //! @brief Displays the most popular quarterly questions.
  public function quarterlyPopularAction() {
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 2;
    $this->view->title = "Domande popolari";
  }


  //! @brief Displays the most popular yearly questions.
  public function yearlyPopularAction() {
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 3;
    $this->view->title = "Domande popolari";
  }


  //! @brief Displays the most popular ever questions.
  public function everPopularAction() {
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 3;
    $this->view->title = "Domande popolari";
  }


  //! @brief Displays the last updated questions.
  public function updatedAction() {
    $this->view->sectionIndex = 2;
    $this->view->title = "Domande aggiornate di recente";
  }


  //! @brief Displays the latest questions based on user's tags.
  public function interestingAction() {
    $this->view->sectionIndex = 1;
    $this->view->title = "Domande interessanti";
  }


  //! @brief Displays the questions, still open, based on user's tags.
  public function stillOpenForMeAction() {
    $this->view->sectionIndex = 0;
    $this->view->subMenu = self::$stillOpenSubMenu;
    $this->view->subIndex = 3;
    $this->view->title = "Domande rivolte a me";
  }


  //! @brief Displays the latest, still open, questions.
  public function stillOpenNewestAction() {
    $this->view->sectionIndex = 0;
    $this->view->subMenu = self::$stillOpenSubMenu;
    $this->view->subIndex = 2;
    $this->view->title = "Domande recenti senza risposta";
  }


  //! @brief Displays the popular, still open, questions.
  public function stillOpenPopularAction() {
    $this->view->sectionIndex = 0;
    $this->view->subMenu = self::$stillOpenSubMenu;
    $this->view->subIndex = 1;
    $this->view->title = "Domande popolari senza risposta";
  }


  //! @brief Displays the questions with no answers.
  public function stillOpenNoAnswerAction() {
    $this->view->sectionIndex = 0;
    $this->view->subMenu = self::$stillOpenSubMenu;
    $this->view->subIndex = 0;
    $this->view->title = "Domande senza nessuna risposta";
  }


  public function rssAction() {
  }

}