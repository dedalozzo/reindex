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

use ReIndex\Model\Member;
use ReIndex\Helper\Time;

use Phalcon\Mvc\View;


/**
 * @brief Controller of Member actions.
 * @nosubgrouping
 */
class MemberController extends ListController {


  public function initialize() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::initialize();

    $this->resultsPerPage = $this->di['config']->application->membersPerPage;

    // FOR DEBUG PURPOSE ONLY UNCOMMENT THE FOLLOWING LINE AND COMMENT THE ONE ABOVE.
    $this->assets->addJs("/reindex/themes/".$this->themeName."/src/js/member.js", FALSE);

    //$this->assets->addJs($this->dist."/js/member.js", FALSE);

    $this->view->pick('views/member');
  }


  public function afterExecuteRoute() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::afterExecuteRoute();
  }


  /**
   * @brief Displays the members with the highest reputation.
   * @param[in] string $filter (optional) Human readable representation of a period.
   */
  public function reputationAction($filter = NULL) {
    $filter = Time::period($filter);
    if ($filter === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->view->setVar('submenu', $this->periods);
    $this->view->setVar('submenuIndex', $period);
    $this->view->setVar('title', 'Utenti per reputazione');
  }


  /**
   * @brief Displays the newest members.
   */
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->reverseOrderOfResults()->setLimit($this->resultsPerPage+1);

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? (int)$_GET['startkey'] : Couch::WildCard();
    $opts->setStartKey($startKey);
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    $rows = $this->couch->queryView("members", "newest", NULL, $opts)->asArray();

    $members = Member::collect(array_column($rows, 'id'));

    if (count($members) > $this->resultsPerPage) {
      $last = array_pop($members);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->createdAt, $last->id));
    }

    $this->view->setVar('members', $members);
    $this->view->setVar('title', 'Nuovi utenti');
  }


  /**
   * @brief Displays the members in alphabetic order.
   */
  public function byNameAction() {
    $opts = new ViewQueryOpts();
    $opts->setLimit($this->resultsPerPage+1);

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? $_GET['startkey'] : chr(0);
    $opts->setStartKey($startKey);
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    $rows = $this->couch->queryView("members", "byUsername", NULL, $opts)->asArray();

    $members = Member::collect(array_column($rows, 'id'));

    if (count($members) > $this->resultsPerPage) {
      $last = array_pop($members);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->username, $last->id));
    }

    $this->view->setVar('members', $members);
    $this->view->setVar('title', 'Utenti per nome');
  }


  /**
   * @brief Displays the list of moderators.
   */
  public function moderatorsAction() {
    $this->view->setVar('title', 'Moderatori');
  }

}