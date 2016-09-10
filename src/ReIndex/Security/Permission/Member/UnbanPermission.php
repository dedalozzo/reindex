<?php

/**
 * @file UnbanPermission.php
 * @brief This file contains the UnbanPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Member;

use ReIndex\Doc\Member;

use EoC\Couch;


/**
 * @brief Permission to remove a ban.
 * @details A moderator (or a member with a superior role) can remove a ban, but only if the member has been
 * banned by an user with an equal (or inferior) role or by himself. And of course he cannot unban himself.
 */
class UnbanPermission extends AbstractPermission {

  /**
   * @var Couch $couch
   */
  protected $couch;


  public function __construct(Member $member) {
    parent::__construct($member);
    $this->couch = $this->di['couchdb'];
  }


  /**
   * @brief A moderator can unban a member has been previously banned.
   * @return bool
   */
  public function checkForModeratorRole() {
    if (!$this->member->isBanned())
      return FALSE;
    elseif ($this->user->match($this->member->bannerId))
      return FALSE;
    elseif ($this->member->bannerId === $this->user->id)
      return TRUE;
    else {
      $whoBanned = $this->couch->getDoc('members', Couch::STD_DOC_PATH, $this->member->bannerId);
      return !$whoBanned->roles->areSuperiorThan($this->getRole(), FALSE) ? TRUE : FALSE;
    }
  }

}