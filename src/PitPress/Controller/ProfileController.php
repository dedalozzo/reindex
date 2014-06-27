<?php

/**
 * @file ProfileController.php
 * @brief This file contains the ProfileController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;

use PitPress\Helper\Stat;

use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use Phalcon\Mvc\View;


/**
 * @brief User's profile controller.
 * @nosubgrouping
 */
class ProfileController extends ListController {

  // Stores the typology sub-menu definition.
  protected static $typologySubMenu = ['libri', 'articoli', 'domande', 'links', 'tutti'];
  protected static $typologyCorrespondence = ['libri' => 'book', 'articoli' => 'article', 'domande' => 'question', 'links' => 'link'];


  /**
   * @brief Displays the newest user's updates.
   */
  public function timelineAction($username, $type = NULL) {
    // If no username is provided, shows all the users.
    if (empty($username))
      return $this->dispatcher->forward(
        [
          'controller' => 'users',
          'action' => 'reputation'
        ]);

    $opts = new ViewQueryOpts();
    $opts->setKey($username)->setLimit(1);
    $result = $this->couch->queryView("users", "byUsername", NULL, $opts);

    // If the user doesn't exist, forward to 404.
    if ($result->isEmpty())
      return $this->dispatcher->forward(
        [
          'controller' => 'error',
          'action' => 'show404'
        ]);

    $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $result[0]['value']);
    $doc->incHits();
    $this->view->setVar('doc', $doc);

    $this->view->setVar('title', $doc->username);

    if (empty($type))
      $type = 'tutti';

    if ($type == 'tutti') {
      $opts = new ViewQueryOpts();
      $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$doc->id, new \stdClass()])->setEndKey([$doc->id]);
      $rows = $this->couch->queryView("posts", "newestByUser", NULL, $opts);
    }
    else {
      $typology = self::$typologyCorrespondence[$type];

      $opts = new ViewQueryOpts();
      $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$doc->id, $typology, new \stdClass()])->setEndKey([$doc->id, $typology]);
      $rows = $this->couch->queryView('posts', 'newestByUserPerType', NULL, $opts);
    }

    $this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getUpdatesCount());

    $this->view->setVar('subsectionMenu', self::$typologySubMenu);
    $this->view->setVar('subsectionIndex', array_flip(self::$typologySubMenu)[$type]);

    $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
    //$this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  public function connectionsAction($username) {

  }


  public function badgesAction($username) {

  }


  public function favoritesAction($username, $type = NULL) {
    // If no user id is provided, shows all the users.
    if (empty($username))
      return $this->dispatcher->forward(
        [
          'controller' => 'users',
          'action' => 'reputation'
        ]);

    $opts = new ViewQueryOpts();
    $opts->setKey($username)->setLimit(1);
    $result = $this->couch->queryView("users", "allNames", NULL, $opts);

    // If the user doesn't exist, forward to 404.
    if ($result->isEmpty())
      return $this->dispatcher->forward(
        [
          'controller' => 'error',
          'action' => 'show404'
        ]);

    $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $result[0]['value']);
    $this->view->setVar('doc', $doc);

    $this->view->setVar('title', $doc->username);

    if (empty($type))
      $type = 'tutti';

    if ($type == 'tutti') {
      $opts = new ViewQueryOpts();
      $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$doc->id, new \stdClass()])->setEndKey([$doc->id]);
      $rows = $this->couch->queryView("favorites", "lastAdded", NULL, $opts);
    }
    else {
      $typology = self::$typologyCorrespondence[$type];

      $opts = new ViewQueryOpts();
      $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$doc->id, $typology, new \stdClass()])->setEndKey([$doc->id, $typology]);
      $rows = $this->couch->queryView('favorites', 'lastAddedPerType', NULL, $opts);
    }

    $this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'value')));

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getUpdatesCount());

    $this->view->setVar('subsectionMenu', self::$typologySubMenu);
    $this->view->setVar('subsectionIndex', array_flip(self::$typologySubMenu)[$type]);

    $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
  }


  public function tagsAction($username) {

  }


  public function reputationAction($username) {

  }


  public function activitiesAction($username) {

  }


  public function bountiesAction($username) {

  }


  public function projectsAction($username) {

  }

} 