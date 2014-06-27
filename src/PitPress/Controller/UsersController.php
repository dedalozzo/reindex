<?php

/**
 * @file UsersController.php
 * @brief Controller of Users actions.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;

use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;
use PitPress\Model\User\User;

use Phalcon\Mvc\View;


/**
 * @brief Controller of Users actions.
 * @nosubgrouping
 */
class UsersController extends BaseController {


  protected function getUsers($keys) {
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
      $user->when = Time::when($result[$i]['value'][2], false);

      $users[] = $user;
    }

    return $users;
  }


  /**
   * @brief Displays the users with the highest reputation.
   */
  public function reputationAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  /**
   * @brief Displays the newest users.
   */
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->reverseOrderOfResults()->setLimit(40);
    $users = $this->couch->queryView("users", "newest", NULL, $opts);

    $this->view->setVar('users', $this->getUsers(array_column($users->asArray(), 'id')));
  }


  /**
   * @brief Displays the users in alphabetic order.
   */
  public function byNameAction() {
    $opts = new ViewQueryOpts();
    $opts->setLimit(40);
    $users = $this->couch->queryView("users", "byUsername", NULL, $opts);

    $this->view->setVar('users', $this->getUsers(array_column($users->asArray(), 'id')));
  }


  /**
   * @brief Displays the users have given most votes.
   */
  public function votersAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  /**
   * @brief Displays the users have edited most posts.
   */
  public function editorsAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  /**
   * @brief Displays the users have added more links.
   */
  public function reportersAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  /**
   * @brief Displays the users have written more posts.
   */
  public function bloggersAction($period) {
    if (empty($period))
      $period = 'settimana';

    $this->view->setVar('subsectionMenu', Time::periods(5));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));
  }


  /**
   * @brief Displays the most popular tags.
   */
  public function moderatorsAction() {
  }


  /**
   * @brief Displays the most popular tags.
   */
  public function privilegesAction() {
  }

}