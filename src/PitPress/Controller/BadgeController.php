<?php

/**
 * @file BadgeController.php
 * @brief This file contains the BadgeController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


/**
 * @brief Controller of Badge actions.
 * @nosubgrouping
 */
class BadgeController extends BaseController {

  protected $badgeLoader;


  /**
   * @brief Initializes the controller.
   */
  public function initialize() {
    $this->badgeLoader = $this->di['badgeloader'];

    parent::initialize();

    $this->view->pick('views/badge');
  }


  /**
   * @brief Displays all badges.
   */
  public function allAction() {
    $badges = $this->badgeLoader->getAllBadges();
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', 'badges disponibili');
    $this->view->setVar('title', 'Tutti i badges');
  }


  /**
   * @brief Displays the achieved badges.
   */
  public function achieveAction() {
    $badges = $this->badgeLoader->getEarnedBadges();
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', 'badges ottenuti');
    $this->view->setVar('title', 'Badges ottenuti');
  }


  /**
   * @brief Displays the not achieved badges.
   */
  public function notAchieveAction() {
    $badges = $this->badgeLoader->getUnearnedBadges();
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', 'badges non ottenuti');
    $this->view->setVar('title', 'Badges non ottenuti');
  }


  /**
   * @brief Displays the gold badges.
   */
  public function goldAction() {
    $badges =  $this->badgeLoader->filterByMetal($this->badgeLoader->getAllBadges(), 'gold');
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', "badges d'oro");
    $this->view->setVar('title', "Badges d'oro");
  }


  /**
   * @brief Displays the silver badges.
   */
  public function silverAction() {
    $badges = $this->badgeLoader->filterByMetal($this->badgeLoader->getAllBadges(), 'silver');
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', "badges d'argento");
    $this->view->setVar('title', "Badges d'argento");
  }


  /**
   * @brief Displays the bronze badges.
   */
  public function bronzeAction() {
    $badges = $this->badgeLoader->filterByMetal($this->badgeLoader->getAllBadges(), 'bronze');
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', 'badges di bronzo');
    $this->view->setVar('title', 'Badges di bronzo');
  }


  /**
   * @brief Displays the special tag badges.
   */
  public function byTagAction($filter = NULL) {
    return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
    // Stores the typology sub-menu definition.
    //protected static $typologySubMenu = ['bronzo', 'argento', 'oro', 'tutti'];

    //$this->view->setVar('submenu', self::$typologySubMenu);
    //$this->view->setVar('submenuIndex', array_flip(self::$typologySubMenu)[$type]);
    $this->view->setVar('title', 'Badges per tag');
  }

}