<?php

/**
 * @file ModeratorRole/UnbanMemberPermission.php
 * @brief This file contains the UnbanMemberPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Doc\Member;

use EoC\Couch;


/**
 * @brief Permission to remove a ban.
 * @details A moderator (or a member with a superior role) can remove a ban, but only if the member has been
 * banned by an user with an equal (or inferior) role or by himself. And of course he cannot unban himself.
 */
class UnbanMemberPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Doc::Member $context
   */
  public function __construct(Member $context = NULL) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Permission to revoke the ban from a member.";
  }


  public function check() {
    if (!$this->context->isBanned())
      return FALSE;
    elseif ($this->user->match($this->context->bannerId))
      return FALSE;
    elseif ($this->context->bannerId === $this->user->id)
      return TRUE;
    else {
      $whoBanned = $this->di['couchdb']->getDoc('members', Couch::STD_DOC_PATH, $this->context->bannerId);
      return !$whoBanned->roles->areSuperiorThan($this->getRole(), FALSE) ? TRUE : FALSE;
    }
  }

}