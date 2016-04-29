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

use ReIndex\Helper;;
use ReIndex\Model\Member;

use Phalcon\Mvc\View;


/**
 * @brief Controller of Member actions.
 * @nosubgrouping
 */
class MemberController extends ListController {


  private function friendshipExists($id) {
    $opts = new ViewQueryOpts();
    $opts->doNotreduce()->setLimit(1)->setKey([$this->user->id, $id]);
    return !$this->couch->queryView("friendships", "approvedPerMember", NULL, $opts)->isEmpty();
  }


  private function getFriendsCount($id) {
    $opts = new ViewQueryOpts();
    $opts->reduce()->reverseOrderOfResults()->setStartKey([$id, Couch::WildCard()])->setEndKey([$id]);
    return $this->couch->queryView("friendships", "approvedPerMember", NULL, $opts)->getReducedValue();
  }


  private function getFollowesCount($id) {
    $opts = new ViewQueryOpts();
    $opts->reduce()->setKey($id);
    return $this->couch->queryView("followers", "perMember", NULL, $opts)->getReducedValue();
  }


  protected function getEntries($keys) {
    if (empty($keys))
      return [];

    $opts = new ViewQueryOpts();

    // Gets the tags properties.
    $opts->doNotReduce();
    $result = $this->couch->queryView("members", "all", $keys, $opts);

    $this->view->setVar('membersCount', $result->getTotalRows());

    // Retrieves the members reputation.
    //$opts->reset();
    //$opts->groupResults()->includeMissingKeys();
    //$reputations = $this->couch->queryView("reputation", "perMember", $keys, $opts);

    $members = [];
    $membersCount = count($result);
    for ($i = 0; $i < $membersCount; $i++) {
      $member = new \stdClass();
      $member->id = $result[$i]['id'];
      $member->username = $result[$i]['value'][0];
      $member->gravatar = Member::getGravatar($result[$i]['value'][1]);
      $member->createdAt = $result[$i]['value'][2];
      $member->fullName = $result[$i]['value'][3] . ' ' . $result[$i]['value'][4];
      $member->headline = $result[$i]['value'][5];
      $member->when = Helper\Time::when($member->createdAt, false);
      $member->hitsCount = Helper\Text::formatNumber($this->redis->hGet(Helper\Text::unversion($member->id), 'hits'));
      $member->friendsCount = Helper\Text::formatNumber($this->getFriendsCount($member->id));
      $member->followersCount = Helper\Text::formatNumber($this->getFollowesCount($member->id));
      $member->friendshipExists = $this->friendshipExists($member->id);

      $members[] = $member;
    }

    return $members;
  }


  public function initialize() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::initialize();

    if ($this->user->isGuest())
      return $this->dispatcher->forward(['controller' => 'auth', 'action' => 'logon']);

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
    $period = $this->getPeriod($filter);
    if ($period === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

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

    $members = $this->couch->queryView("members", "newest", NULL, $opts)->asArray();

    $entries = $this->getEntries(array_column($members, 'id'));

    if (count($entries) > $this->resultsPerPage) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->createdAt, $last->id));
    }

    $this->view->setVar('members', $entries);
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

    $members = $this->couch->queryView("members", "byUsername", NULL, $opts)->asArray();

    $entries = $this->getEntries(array_column($members, 'id'));

    if (count($entries) > $this->resultsPerPage) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->username, $last->id));
    }

    $this->view->setVar('members', $entries);
    $this->view->setVar('title', 'Utenti per nome');
  }


  /**
   * @brief Displays the list of moderators.
   */
  public function moderatorsAction() {
    $this->view->setVar('title', 'Moderatori');
  }

}