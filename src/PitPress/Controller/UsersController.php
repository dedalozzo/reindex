<?php

//! @file UsersController.php
//! @brief Controller of Users actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;

use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;


//! @brief Controller of Users actions.
//! @nosubgrouping
class UsersController extends ListController {

  protected static $sectionLabel = 'UTENTI';

  // Stores the main menu definition.
  protected static $sectionMenu = [
    ['name' => 'privileges', 'path' => '/privilegi/', 'label' => 'PRIVILEGI', 'title' => 'Privilegi'],
    ['name' => 'moderators', 'path' => '/moderatori/', 'label' => 'MODERATORI', 'title' => 'Moderatori'],
    ['name' => 'bloggers', 'path' => '/bloggers/', 'label' => 'BLOGGERS', 'title' => 'Bloggers'],
    ['name' => 'reporters', 'path' => '/reporters/', 'label' => 'REPORTERS', 'title' => 'Reporters'],
    ['name' => 'editors', 'path' => '/editori/', 'label' => 'EDITORI', 'title' => 'Editori'],
    ['name' => 'voters', 'path' => '/votanti/', 'label' => 'VOTANTI', 'title' => 'Votanti'],
    ['name' => 'newest', 'path' => '/nuovi/', 'label' => 'NUOVI', 'title' => 'Nuovi utenti'],
    ['name' => 'reputation', 'path' => '/reputazione/', 'label' => 'REPUTAZIONE', 'title' => 'Reputazione utenti']
  ];


  //! @brief Displays the users with the highest reputation.
  public function reputationAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  //! @brief Displays the newest users.
  public function newestAction() {
  }


  //! @brief Displays the users have given most votes.
  public function votersAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  //! @brief Displays the users have edited most posts.
  public function editorsAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  //! @brief Displays the users have added more links.
  public function reportersAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  //! @brief Displays the users have written more posts.
  public function bloggersAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  //! @brief Displays the most popular tags.
  public function moderatorsAction() {
  }


  //! @brief Displays the most popular tags.
  public function privilegesAction() {
  }

}