<?php

/**
 * @file BadgeController.php
 * @brief This file contains the BadgeController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


/**
 * @brief Controller of Badge actions.
 * @nosubgrouping
 */
class BadgeController extends BaseController {

  protected $committee;


  /**
   * @brief Initializes the controller.
   */
  public function initialize() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    $this->committee = $this->di['committee'];

    parent::initialize();

    $this->view->pick('views/badge');
  }


  public function afterExecuteRoute() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::afterExecuteRoute();
  }

  /**
   * @brief Displays all badges.
   */
  public function allAction() {
    $badges = $this->committee->getDecorators();
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', 'badges disponibili');
    $this->view->setVar('title', 'Tutti i badges');
  }


  /**
   * @brief Displays the earned badges.
   */
  public function earnedAction() {
    $badges = $this->committee->getEarnedBadges();
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', 'badges ottenuti');
    $this->view->setVar('title', 'Badges ottenuti');
  }


  /**
   * @brief Displays the unearned badges.
   */
  public function unearnedAction() {
    $badges = $this->committee->getUnearnedBadges();
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', 'badges non ottenuti');
    $this->view->setVar('title', 'Badges non ottenuti');
  }


  /**
   * @brief Displays the gold badges.
   */
  public function goldAction() {
    $badges =  $this->committee->filterByMetal('gold');
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', "badges d'oro");
    $this->view->setVar('title', "Badges d'oro");
  }


  /**
   * @brief Displays the silver badges.
   */
  public function silverAction() {
    $badges = $this->committee->filterByMetal('silver');
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', "badges d'argento");
    $this->view->setVar('title', "Badges d'argento");
  }


  /**
   * @brief Displays the bronze badges.
   */
  public function bronzeAction() {
    $badges = $this->committee->filterByMetal('bronze');
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', 'badges di bronzo');
    $this->view->setVar('title', 'Badges di bronzo');
  }


  /**
   * @brief Displays the special tag badges.
   * @param[in] string $filter (optional) The filter typology.
   */
  public function tagAction($filter = NULL) {
    return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
    // Stores the typology sub-menu definition.
    //protected static $typologySubMenu = ['bronzo', 'argento', 'oro', 'tutti'];

    //$this->view->setVar('submenu', self::$typologySubMenu);
    //$this->view->setVar('submenuIndex', array_flip(self::$typologySubMenu)[$type]);
    $this->view->setVar('title', 'Badges per tag');
  }

}