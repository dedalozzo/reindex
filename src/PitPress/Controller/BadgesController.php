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
    $this->view->setVar('badges', $this->badgeLoader->getAllBadges());
  }


  /**
   * @brief Displays the achieved badges.
   */
  public function achieveAction() {
    $this->view->setVar('badges', $this->badgeLoader->getEarnedBadges());
  }


  /**
   * @brief Displays the not achieved badges.
   */
  public function notAchieveAction() {
    $this->view->setVar('badges', $this->badgeLoader->getUnearnedBadges());
  }


  /**
   * @brief Displays the gold badges.
   */
  public function goldAction() {
    $this->view->setVar('badges', $this->badgeLoader->filterByMetal($this->badgeLoader->getAllBadges(), 'gold'));
  }


  /**
   * @brief Displays the silver badges.
   */
  public function silverAction() {
    $this->view->setVar('badges', $this->badgeLoader->filterByMetal($this->badgeLoader->getAllBadges(), 'silver'));
  }


  /**
   * @brief Displays the bronze badges.
   */
  public function bronzeAction() {
    $this->view->setVar('badges', $this->badgeLoader->filterByMetal($this->badgeLoader->getAllBadges(), 'bronze'));
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