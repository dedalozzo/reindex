<?php

//! @file UsersController.php
//! @brief Controller of Users actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


//! @brief Controller of Users actions.
//! @nosubgrouping
class UsersController extends ListController {

  protected static $controllerPath = '/';
  protected static $controllerIndex = 6;
  protected static $controllerLabel = 'UTENTI';

  // Stores the main menu definition.
  protected static $actionMenu = [
    ['link' => 'privilegi/', 'name' => 'PRIVILEGI'],
    ['link' => 'moderatori/', 'name' => 'MODERATORI'],
    ['link' => 'bloggers/', 'name' => 'BLOGGERS'],
    ['link' => 'reporters/', 'name' => 'REPORTERS'],
    ['link' => 'editori/', 'name' => 'EDITORI'],
    ['link' => 'votanti/', 'name' => 'VOTANTI'],
    ['link' => 'utenti/', 'name' => 'NUOVI'],
    ['link' => 'reputazione/', 'name' => 'REPUTAZIONE']
  ];

  // Stores the voters sub-menu definition.
  protected static $periodSubMenu = [
    ['link' => 'sempre/', 'name' => 'SEMPRE'],
    ['link' => 'anno/', 'name' => 'ANNO'],
    ['link' => 'trimestre/', 'name' => 'TRIMESTRE'],
    ['link' => 'mese/', 'name' => 'MESE'],
    ['link' => 'settimana/', 'name' => 'SETTIMANA']
  ];


  //! @brief Displays the index.
  public function indexAction() {
    $this->weeklyReputationAction();
  }


  //! @brief Displays the users with the highest weekly reputation.
  public function weeklyReputationAction() {
    $this->initAction('reputazione/', 7, 'Reputazione utenti', self::$periodSubMenu, 4);
  }


  //! @brief Displays the users with the highest monthly reputation.
  public function monthlyReputationAction() {
    $this->initAction('reputazione/', 7, 'Reputazione utenti', self::$periodSubMenu, 3);
  }


  //! @brief Displays the users with the highest quarterly reputation.
  public function quarterlyReputationAction() {
    $this->initAction('reputazione/', 7, 'Reputazione utenti', self::$periodSubMenu, 2);
  }


  //! @brief Displays the users with the highest yearly reputation.
  public function yearlyReputationAction() {
    $this->initAction('reputazione/', 7, 'Reputazione utenti', self::$periodSubMenu, 1);
  }


  //! @brief Displays the users with the highest reputation.
  public function everReputationAction() {
    $this->view->sectionIndex = 7;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 0;
    $this->view->title = "Reputazione utenti";
  }


  //! @brief Displays the newest users.
  public function newestAction() {
    $this->view->sectionIndex = 6;
  }


  //! @brief Displays the users have given most votes during the week.
  public function weeklyVotersAction() {
    $this->view->basePath = '/votanti/';
    $this->view->sectionIndex = 5;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 4;
    $this->view->title = "Reputazione votanti";
  }


  //! @brief Displays the users have given most votes during the month.
  public function monthlyVotersAction() {
    $this->view->basePath = '/votanti/';
    $this->view->sectionIndex = 5;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 3;
    $this->view->title = "Reputazione votanti";
  }


  //! @brief Displays the users have given most votes during the quarter.
  public function quarterlyVotersAction() {
    $this->view->basePath = '/votanti/';
    $this->view->sectionIndex = 5;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 2;
    $this->view->title = "Reputazione votanti";
  }


  //! @brief Displays the users have given most votes during the year.
  public function yearlyVotersAction() {
    $this->view->basePath = '/votanti/';
    $this->view->sectionIndex = 5;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 1;
    $this->view->title = "Reputazione votanti";
  }


  //! @brief Displays the users have given most votes ever.
  public function everVotersAction() {
    $this->view->basePath .= '/votanti/';
    $this->view->sectionIndex = 5;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 0;
    $this->view->title = "Reputazione votanti";
  }


  //! @brief Displays the users have edited most posts during the week.
  public function weeklyEditorsAction() {
    $this->view->basePath .= 'editori/';
    $this->view->sectionIndex = 4;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 4;
    $this->view->title = "Reputazione editori";
  }


  //! @brief Displays the users have edited most posts during the month.
  public function monthlyEditorsAction() {
    $this->view->basePath .= 'editori/';
    $this->view->sectionIndex = 4;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 3;
    $this->view->title = "Reputazione editori";
  }


  //! @brief Displays the users have edited most posts during the quarter.
  public function quarterlyEditorsAction() {
    $this->view->basePath .= 'editori/';
    $this->view->sectionIndex = 4;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 2;
    $this->view->title = "Reputazione editori";
  }


  //! @brief Displays the users have edited most posts during the year.
  public function yearlyEditorsAction() {
    $this->view->basePath .= 'editori/';
    $this->view->sectionIndex = 4;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 1;
    $this->view->title = "Reputazione editori";
  }


  //! @brief Displays the users have edited most posts ever.
  public function everEditorsAction() {
    $this->view->basePath .= 'editori/';
    $this->view->sectionIndex = 4;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 0;
    $this->view->title = "Reputazione editori";
  }


  //! @brief Displays the users have added more links during the week.
  public function weeklyReportersAction() {
    $this->view->basePath .= 'reporters/';
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 3;
    $this->view->title = "Reputazione reporters";
  }


  //! @brief Displays the users have added more links during the month.
  public function monthlyReportersAction() {
    $this->view->basePath .= 'reporters/';
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 3;
    $this->view->title = "Reputazione reporters";
  }


  //! @brief Displays the users have added more links during the quarter.
  public function quarterlyReportersAction() {
    $this->view->basePath .= 'reporters/';
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 2;
    $this->view->title = "Reputazione reporters";
  }


  //! @brief Displays the users have added more links during the year.
  public function yearlyReportersAction() {
    $this->view->basePath .= 'reporters/';
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 1;
    $this->view->title = "Reputazione reporters";
  }


  //! @brief Displays the users have added more links ever.
  public function everReportersAction() {
    $this->view->basePath .= 'reporters/';
    $this->view->sectionIndex = 3;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 0;
    $this->view->title = "Reputazione reporters";
  }


  //! @brief Displays the users have written more posts during the week.
  public function weeklyBloggersAction() {
    $this->view->basePath .= 'bloggers/';
    $this->view->sectionIndex = 2;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 2;
    $this->view->title = "Reputazione bloggers";
  }


  //! @brief Displays the users have written more posts during the month.
  public function monthlyBloggersAction() {
    $this->view->basePath .= 'bloggers/';
    $this->view->sectionIndex = 2;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 2;
    $this->view->title = "Reputazione bloggers";
  }


  //! @brief Displays the users have written more posts during the quarter.
  public function quarterlyBloggersAction() {
    $this->view->basePath .= 'bloggers/';
    $this->view->sectionIndex = 2;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 2;
    $this->view->title = "Reputazione bloggers";
  }


  //! @brief Displays the users have written more posts during the year.
  public function yearlyBloggersAction() {
    $this->view->basePath .= 'bloggers/';
    $this->view->sectionIndex = 2;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 1;
    $this->view->title = "Reputazione bloggers";
  }


  //! @brief Displays the users have written more posts ever.
  public function everBloggersAction() {
    $this->view->basePath .= 'bloggers/';
    $this->view->sectionIndex = 2;
    $this->view->subMenu = self::$periodSubMenu;
    $this->view->subIndex = 0;
    $this->view->title = "Reputazione bloggers";
  }


  //! @brief Displays the most popular tags.
  public function moderatorsAction() {
    $this->view->sectionIndex = 1;
    $this->view->title = "Moderatori";
  }


  //! @brief Displays the most popular tags.
  public function privilegesAction() {
    $this->view->sectionIndex = 0;
    $this->view->title = "Privilegi";
  }

}