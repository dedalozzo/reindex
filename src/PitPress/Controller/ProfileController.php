<?php

//! @file ProfileController.php
//! @brief This file contains the ProfileController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;

use PitPress\Helper\Stat;

use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use Phalcon\Mvc\View;

//! @brief User's profile controller.
//! @nosubgrouping
class ProfileController extends ListController {

  // Stores the typology sub-menu definition.
  protected static $typologySubMenu = ['guide', 'libri', 'articoli', 'domande', 'links', 'tutti'];


  //! @brief Displays the newest user's updates.
  public function timelineAction($id) {
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


    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$doc->id, new \stdClass()])->setEndKey([$doc->id]);
    $rows = $this->couch->queryView("posts", "newestByUser", NULL, $opts)['rows'];

    $this->view->setVar('entries', $this->getEntries(array_column($rows, 'id')));

    $stat = new Stat();
    $this->view->setVar('entriesCount', $stat->getUpdatesCount());

    if (empty($type))
      $type = 'tutti';

    $this->view->setVar('subsectionMenu', self::$typologySubMenu);
    $this->view->setVar('subsectionIndex', array_flip(self::$typologySubMenu)[$type]);

    $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
    //$this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  public function connectionsAction($id) {

  }


  public function badgesAction($id) {

  }


  public function favoritesAction($id) {

  }


  public function tagsAction($id) {

  }


  public function reputationAction($id) {

  }


  public function activitiesAction($id) {

  }


  public function bountiesAction($id) {

  }

} 