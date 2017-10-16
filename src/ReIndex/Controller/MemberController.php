<?php

/**
 * @file MemberController.php
 * @brief This file contains the MemberController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;

use EoC\Opt\ViewQueryOpts;

use ReIndex\Doc\Member;

use ToolBag\Helper;

use Phalcon\Mvc\View;


/**
 * @brief Controller of Member actions.
 * @nosubgrouping
 */
final class MemberController extends ListController {


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
   * @brief Returns all defined roles' names.
   * @return array of strings
   */
  protected function getRoles() {
    $roles = $this->guardian->allRoles();

    $names['all'] = NULL;
    foreach ($roles as $role) {
      $names[$role->getName()] = NULL;
    }

    return $names;
  }


  /**
   * @brief Displays the members from the most popular to the lowest.
   */
  public function popularAction() {
    $this->view->setVar('title', 'Most popular users');
  }


  /**
   * @brief Displays the newest members, even filtered by role.
   * @param[in] string $role (optional) A role name.
   * @param[in] string $period (optional) A human readable period of time.
   */
  public function newestAction($role = 'all', $period = NULL) {
    $roles = $this->getRoles();
    $periods = Helper\ArrayHelper::slice(Helper\TimeHelper::$periods, 7);

    $role = Helper\ArrayHelper::key($role, $roles);
    $period = Helper\TimeHelper::period($period);

    if ($role === FALSE || $period === FALSE)
      return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $min = 0; $max = 0;
    Helper\TimeHelper::minMaxInPeriod($period, $min, $max);

    $opts = new ViewQueryOpts();
    $opts->reverseOrderOfResults()->setLimit($this->resultsPerPage+1);

    $startKey = isset($_GET['startkey']) ? (int)$_GET['startkey'] : $max;
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    if ($role === 'all') {
      $opts->setStartKey($startKey)->setEndKey($min);
      // members/newest/view
      $rows = $this->couch->queryView('members', 'newest', 'view', NULL, $opts)->asArray();
    }
    else {
      $opts->setStartKey([$role, $startKey])->setEndKey([$role, $min]);
      // members/byRole/view
      $rows = $this->couch->queryView('members', 'byRole', 'view', NULL, $opts)->asArray();
    }

    $members = Member::collect(array_column($rows, 'id'));

    if (count($members) > $this->resultsPerPage) {
      $last = array_pop($members);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->createdAt, $last->id));
    }

    $this->dispatcher->setParam('role', $role);
    $this->dispatcher->setParam('period', $period);

    $this->view->setVar('entries', $members);
    $this->view->setVar('roles', $roles);
    $this->view->setVar('periods', $periods);
    $this->view->setVar('title', 'New users');
  }

}