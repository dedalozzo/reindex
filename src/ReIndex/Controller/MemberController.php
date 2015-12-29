<?php

/**
 * @file MemberController.php
 * @brief This file contains the MemberController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;

use EoC\Opt\ViewQueryOpts;
use EoC\Couch;

use ReIndex\Helper\Time;
use ReIndex\Model\Member;

use Phalcon\Mvc\View;


/**
 * @brief Controller of Member actions.
 * @nosubgrouping
 */
class MemberController extends ListController {


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
      $user->gravatar = Member::getGravatar($result[$i]['value'][1]);
      $user->createdAt = $result[$i]['value'][2];
      $user->when = Time::when($result[$i]['value'][2], false);

      $users[] = $user;
    }

    return $users;
  }


  public function initialize() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::initialize();

    $this->resultsPerPage = $this->di['config']->application->usersPerPage;
    $this->view->pick('views/user');
  }


  public function afterExecuteRoute() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::afterExecuteRoute();
  }


  /**
   * @brief Displays the users with the highest reputation.
   * @param[in] string $filter (optional) Human readable representation of a period.
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

    $users = $this->couch->queryView("members", "byUsername", NULL, $opts)->asArray();

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
   * @param[in] string $filter (optional) Human readable representation of a period.
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