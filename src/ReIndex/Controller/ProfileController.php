<?php

/**
 * @file ProfileController.php
 * @brief This file contains the ProfileController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;

use ReIndex\Helper;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use Phalcon\Mvc\View;


/**
 * @brief Member's profile controller.
 * @nosubgrouping
 */
class ProfileController extends ListController {


  /**
   * @brief Given a username returns the correspondent user.
   */
  protected function getUser($username) {
    $this->log->addDebug(sprintf('Username: %s', $username));

    $opts = new ViewQueryOpts();
    $opts->setKey($username)->setLimit(1);
    $result = $this->couch->queryView("members", "byUsername", NULL, $opts);

    if ($result->isEmpty()) return NULL;

    $user = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $result[0]['value']);
    $user->incHits($this->user->id);

    $this->view->setVar('profile', $user);

    return $user;
  }


  public function initialize() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::initialize();

    $this->resultsPerPage = $this->di['config']->application->postsPerPage;
  }


  public function afterExecuteRoute() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::afterExecuteRoute();

    $this->view->pick('views/profile/timeline');
  }


  /**
   * @brief Displays the newest user's contributes.
   */
  public function indexAction($username) {
    $user = $this->getUser($username);

    // If the user doesn't exist, forward to 404.
    if (!$user->isMember()) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $opts = new ViewQueryOpts();

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? (int)$_GET['startkey'] : Couch::WildCard();
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    $opts->doNotReduce()->setLimit($this->resultsPerPage+1)->reverseOrderOfResults()->setStartKey([$user->id, $startKey])->setEndKey([$user->id]);
    $rows = $this->couch->queryView("posts", "perDateByUser", NULL, $opts);

    $opts->reduce()->setStartKey([$user->id, Couch::WildCard()])->unsetOpt('startkey_docid');
    $count = $this->couch->queryView("posts", "perDateByUser", NULL, $opts)->getReducedValue();

    $entries = $this->getEntries(array_column($rows->asArray(), 'id'));

    if (count($entries) > $this->resultsPerPage) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->publishedAt, $last->id));
    }

    $this->view->setVar('entries', $entries);
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));
    $this->view->setVar('entriesLabel', 'contributi');
    $this->view->setVar('title', sprintf('%s timeline', $username));
  }


  public function aboutAction($username) {

  }


  public function connectionsAction($username) {

  }


  public function projectsAction($username) {

  }


  public function activitiesAction($username) {

  }


  public function settingsAction($username) {

  }

} 