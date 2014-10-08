<?php

/**
 * @file UserController.php
 * @brief This file contains the UserController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;

use ElephantOnCouch\Opt\ViewQueryOpts;
use ElephantOnCouch\Couch;

use PitPress\Helper\Time;
use PitPress\Model\User;

use Phalcon\Mvc\View;


/**
 * @brief Controller of User actions.
 * @nosubgrouping
 */
class UserController extends ListController {


  protected function getEntries($keys) {
    if (empty($keys))
      return [];

    $opts = new ViewQueryOpts();

    // Gets the tags properties.
    $opts->doNotReduce();
    $result = $this->couch->queryView("users", "all", $keys, $opts);

    $this->view->setVar('usersCount', $result->getTotalRows());

    // Retrieves the users reputation.
    //$opts->reset();
    //$opts->groupResults()->includeMissingKeys();
    //$reputations = $this->couch->queryView("reputation", "perUser", $keys, $opts);

    $users = [];
    $usersCount = count($result);
    for ($i = 0; $i < $usersCount; $i++) {
      $user = new \stdClass();
      $user->id = $result[$i]['id'];
      $user->username = $result[$i]['value'][0];
      $user->gravatar = User::getGravatar($result[$i]['value'][1]);
      $user->createdAt = $result[$i]['value'][2];
      $user->when = Time::when($result[$i]['value'][2], false);

      $users[] = $user;
    }

    return $users;
  }


  public function initialize() {
    parent::initialize();
    $this->resultsPerPage = $this->di['config']->application->usersPerPage;
    $this->view->pick('views/user');
  }


  /**
   * @brief Displays the users with the highest reputation.
   */
  public function reputationAction($filter = NULL) {
    $period = $this->getPeriod($filter);
    if ($period === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->view->setVar('submenu', $this->periods);
    $this->view->setVar('submenuIndex', $period);
    $this->view->setVar('title', 'Utenti per reputazione');
  }


  /**
   * @brief Displays the newest users.
   */
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->reverseOrderOfResults()->setLimit($this->resultsPerPage+1);

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? (int)$_GET['startkey'] : Couch::WildCard();
    $opts->setStartKey($startKey);
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    $users = $this->couch->queryView("users", "newest", NULL, $opts)->asArray();

    $entries = $this->getEntries(array_column($users, 'id'));

    if (count($entries) > $this->resultsPerPage) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->createdAt, $last->id));
    }

    $this->view->setVar('users', $entries);
    $this->view->setVar('title', 'Nuovi utenti');
  }


  /**
   * @brief Displays the users in alphabetic order.
   */
  public function byNameAction() {
    $opts = new ViewQueryOpts();
    $opts->setLimit($this->resultsPerPage+1);

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? $_GET['startkey'] : chr(0);
    $opts->setStartKey($startKey);
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    $users = $this->couch->queryView("users", "byUsername", NULL, $opts)->asArray();

    $entries = $this->getEntries(array_column($users, 'id'));

    if (count($entries) > $this->resultsPerPage) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->username, $last->id));
    }

    $this->view->setVar('users', $entries);
    $this->view->setVar('title', 'Utenti per nome');
  }


  /**
   * @brief Displays the users have given most votes.
   */
  public function votersAction($filter = NULL) {
    $period = $this->getPeriod($filter);
    if ($period === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->view->setVar('submenu', $this->periods);
    $this->view->setVar('submenuIndex', $period);
    $this->view->setVar('title', 'Utenti votanti');
  }


  /**
   * @brief Displays the list of moderators.
   */
  public function moderatorsAction() {
    $this->view->setVar('title', 'Moderatori');
  }


  /**
   * @brief Displays the most popular tags.
   */
  public function privilegesAction() {
    $this->view->setVar('title', 'Privilegi');
  }

}