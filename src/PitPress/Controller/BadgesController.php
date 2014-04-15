<?php

//! @file BadgesController.php
//! @brief Controller of Badges actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


//! @brief Controller of Badges actions.
//! @nosubgrouping
class BadgesController extends ListController {

  // Stores the typology sub-menu definition.
  protected static $typologySubMenu = ['bronzo', 'argento', 'oro', 'tutti'];


  //! @brief Displays all badges.
  public function allAction() {
  }


  //! @brief Displays the achieved badges.
  public function achieveAction() {
  }


  //! @brief Displays the not achieved badges.
  public function notAchieveAction() {
  }


  //! @brief Displays the gold badges.
  public function goldAction() {
  }


  //! @brief Displays the silver badges.
  public function silverAction() {
  }


  //! @brief Displays the bronze badges.
  public function bronzeAction() {
  }


  //! @brief Displays the special tag badges.
  public function byTagAction($type) {
    if (empty($type))
      $type = 'tutti';

    $this->view->setVar('subsectionMenu', self::$typologySubMenu);
    $this->view->setVar('subsectionIndex', array_flip(self::$typologySubMenu)[$type]);
  }

}