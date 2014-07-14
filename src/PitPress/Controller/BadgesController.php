<?php

/**
 * @file BadgesController.php
 * @brief Controller of Badges actions.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


/**
 * @brief Controller of Badges actions.
 * @nosubgrouping
 */
class BadgesController extends ListController {

  protected $badgeLoader;


  // Stores the typology sub-menu definition.
  protected static $typologySubMenu = ['bronzo', 'argento', 'oro', 'tutti'];


  /**
   * @brief Initializes the controller.
   */
  public function initialize() {
    $this->badgeLoader = $this->di['badgeloader'];

    parent::initialize();
  }


  /**
   * @brief Displays all badges.
   */
  public function allAction() {
    $badges = $this->badgeLoader->getAllBadges();
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', 'badges disponibili');
  }


  /**
   * @brief Displays the achieved badges.
   */
  public function achieveAction() {
    $badges = $this->badgeLoader->getEarnedBadges();
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', 'badges ottenuti');
  }


  /**
   * @brief Displays the not achieved badges.
   */
  public function notAchieveAction() {
    $badges = $this->badgeLoader->getUnearnedBadges();
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', 'badges non ottenuti');
  }


  /**
   * @brief Displays the gold badges.
   */
  public function goldAction() {
    $badges =  $this->badgeLoader->filterByMetal($this->badgeLoader->getAllBadges(), 'gold');
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', "badges d'oro");
  }


  /**
   * @brief Displays the silver badges.
   */
  public function silverAction() {
    $badges = $this->badgeLoader->filterByMetal($this->badgeLoader->getAllBadges(), 'silver');
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', "badges d'argento");
  }


  /**
   * @brief Displays the bronze badges.
   */
  public function bronzeAction() {
    $badges = $this->badgeLoader->filterByMetal($this->badgeLoader->getAllBadges(), 'bronze');
    $this->view->setVar('badges', $badges);
    $this->view->setVar('entriesCount', count($badges));
    $this->view->setVar('entriesLabel', 'badges di bronzo');
  }


  /**
   * @brief Displays the special tag badges.
   */
  public function byTagAction($type) {
    if (empty($type))
      $type = 'tutti';

    $this->view->setVar('subsectionMenu', self::$typologySubMenu);
    $this->view->setVar('subsectionIndex', array_flip(self::$typologySubMenu)[$type]);
  }

}