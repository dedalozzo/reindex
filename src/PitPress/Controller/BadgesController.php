<?php

//! @file BadgesController.php
//! @brief Controller of Badges actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


//! @brief Controller of Badges actions.
//! @nosubgrouping
class BadgesController extends BaseController {

  protected static $controllerPath = '/badges/';
  protected static $controllerIndex = 5;
  protected static $controllerLabel = 'BADGES';

  // Stores the main menu definition.
  protected static $actionMenu = [
    ['link' => 'per-tag/', 'name' => 'PER TAG'],
    ['link' => 'bronzo/', 'name' => 'BRONZO'],
    ['link' => 'argento/', 'name' => 'ARGENTO'],
    ['link' => 'oro/', 'name' => 'ORO'],
    ['link' => 'non-ottenuti/', 'name' => 'NON OTTENUTI'],
    ['link' => 'ottenuti/', 'name' => 'OTTENUTI'],
    ['link' => 'tutti/', 'name' => 'TUTTI']
  ];

  // Stores the typology sub-menu definition.
  protected static $typologySubMenu = [
    ['link' => 'bronzo/', 'name' => 'BRONZO'],
    ['link' => 'argento/', 'name' => 'ARGENTO'],
    ['link' => 'oro/', 'name' => 'ORO'],
    ['link' => 'tutti/', 'name' => 'TUTTI']
  ];


  //! Displays the index.
  public function indexAction() {
    $this->allAction();
  }


  //! @brief Displays all badges.
  public function allAction() {
    $this->view->sectionIndex = 6;
    $this->view->title = "Tutti i badges";
  }


  //! @brief Displays the achieved badges.
  public function achieveAction() {
    $this->view->sectionIndex = 5;
    $this->view->title = "I miei badges";
  }


  //! @brief Displays the not achieved badges.
  public function notAchieveAction() {
    $this->view->sectionIndex = 4;
    $this->view->title = "Badges mancanti";
  }


  //! @brief Displays the gold badges.
  public function goldAction() {
    $this->view->sectionIndex = 3;
    $this->view->title = "Badges d'oro";
  }


  //! @brief Displays the silver badges.
  public function silverAction() {
    $this->view->sectionIndex = 2;
    $this->view->title = "Badges d'argento";
  }


  //! @brief Displays the bronze badges.
  public function bronzeAction() {
    $this->view->sectionIndex = 1;
    $this->view->title = "Badges di bronzo";
  }


  //! @brief Displays the special tag badges.
  public function allByTagAction() {
    $this->view->sectionIndex = 0;
    $this->view->subMenu = self::$typologySubMenu;
    $this->view->subIndex = 3;
    $this->view->title = "Tutti i badges per tag";
  }


  //! @brief Displays the gold special tag badges.
  public function goldByTagAction() {
    $this->view->sectionIndex = 0;
    $this->view->subMenu = self::$typologySubMenu;
    $this->view->subIndex = 2;
    $this->view->title = "Badges d'oro per tag";
  }


  //! @brief Displays the silver special tag badges.
  public function silverByTag() {
    $this->view->sectionIndex = 0;
    $this->view->subMenu = self::$typologySubMenu;
    $this->view->subIndex = 1;
    $this->view->title = "Badges d'argento per tag";
  }


  //! @brief Displays the bronze special tag badges.
  public function bronzeByTag() {
    $this->view->sectionIndex = 0;
    $this->view->subMenu = self::$typologySubMenu;
    $this->view->subIndex = 0;
    $this->view->title = "Badges di bronzo per tag";
  }

}