<?php

//! @file UsersController.php
//! @brief Controller of Users actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;

use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;
use PitPress\Model\User\User;

use Phalcon\Mvc\View;


//! @brief Controller of Users actions.
//! @nosubgrouping
class UsersController extends BaseController {


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


  public function showAction($id) {
    // If no user id is provided, shows all the users.
    if (empty($id))
      return $this->dispatcher->forward(
        [
          'controller' => 'users',
          'action' => 'reputation'
        ]);

    $opts = new ViewQueryOpts();
    $opts->setKey($id)->setLimit(1);
    $rows = $this->couch->queryView("users", "allNames", NULL, $opts)['rows'];

    // If the user doesn't exist, forward to 404.
    if (empty($rows))
      return $this->dispatcher->forward(
        [
          'controller' => 'error',
          'action' => 'show404'
        ]);

    $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $id);
    $doc->incHits();
    $this->view->setVar('doc', $doc);

    $this->view->setVar('title', $doc->displayName);

    $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
    $this->view->disableLevel(View::LEVEL_LAYOUT);
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