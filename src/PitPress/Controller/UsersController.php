<?php

//! @file UsersController.php
//! @brief Controller of Users actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;

use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;
use PitPress\Model\User\User;

use Phalcon\Mvc\View;


//! @brief Controller of Users actions.
//! @nosubgrouping
class UsersController extends SectionController {

  protected static $sectionLabel = 'UTENTI';

  // Stores the main menu definition.
  protected static $sectionMenu = [
    ['name' => 'privileges', 'path' => '/privilegi/', 'label' => 'PRIVILEGI', 'title' => 'Privilegi'],
    ['name' => 'moderators', 'path' => '/moderatori/', 'label' => 'MODERATORI', 'title' => 'Moderatori'],
    ['name' => 'bloggers', 'path' => '/bloggers/', 'label' => 'BLOGGERS', 'title' => 'Bloggers'],
    ['name' => 'reporters', 'path' => '/reporters/', 'label' => 'REPORTERS', 'title' => 'Reporters'],
    ['name' => 'editors', 'path' => '/editori/', 'label' => 'EDITORI', 'title' => 'Editori'],
    ['name' => 'voters', 'path' => '/votanti/', 'label' => 'VOTANTI', 'title' => 'Votanti'],
    ['name' => 'byName', 'path' => '/per-nome/', 'label' => 'PER NOME', 'title' => 'Utenti in ordine alfabetico'],
    ['name' => 'newest', 'path' => '/nuovi/', 'label' => 'NUOVI', 'title' => 'Nuovi utenti'],
    ['name' => 'reputation', 'path' => '/reputazione/', 'label' => 'REPUTAZIONE', 'title' => 'Reputazione utenti']
  ];


  protected function getEntries($keys) {
    if (empty($keys))
      return [];

    $opts = new ViewQueryOpts();

    // Gets the tags properties.
    $opts->doNotReduce();
    $result = $this->couch->queryView("users", "all", $keys, $opts);

    $this->view->setVar('usersCount', $result['total_rows']);
    $users = $result['rows'];

    // Retrieves the users reputation.
    //$opts->reset();
    //$opts->groupResults()->includeMissingKeys();
    //$reputations = $this->couch->queryView("reputation", "perUser", $keys, $opts)['rows'];

    $entries = [];
    $usersCount = count($users);
    for ($i = 0; $i < $usersCount; $i++) {
      $entry = new \stdClass();
      $entry->id = $users[$i]['id'];
      $entry->displayName = $users[$i]['value'][0];
      $entry->gravatar = User::getGravatar($users[$i]['value'][1]);
      $entry->when = Time::when($users[$i]['value'][2], false);

      $entries[] = $entry;
    }

    return $entries;
  }


  public function signInAction() {
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  public function signUpAction() {
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  public function resetPasswordAction() {
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  public function sendActivationEmailAction() {
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  public function showAction($id) {
    if (empty($id))
      $this->dispatcher->forward(
        [
          'controller' => 'users',
          'action' => 'reputation'
        ]);
  }


  //! @brief Displays the users with the highest reputation.
  public function reputationAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  //! @brief Displays the newest users.
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->reverseOrderOfResults()->setLimit(40);
    $users = $this->couch->queryView("users", "newest", NULL, $opts)['rows'];

    $this->view->setVar('entries', $this->getEntries(array_column($users, 'id')));
  }


  //! @brief Displays the users in alphabetic order.
  public function byNameAction() {
    $opts = new ViewQueryOpts();
    $opts->setLimit(40);
    $users = $this->couch->queryView("users", "byDisplayName", NULL, $opts)['rows'];

    $this->view->setVar('entries', $this->getEntries(array_column($users, 'id')));
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