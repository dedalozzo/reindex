<?php

/**
 * @file UnbanMemberPermission.php
 * @brief This file contains the UnbanMemberPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use EoC\Couch;


/**
 * @brief Permission to remove a ban.
 */
class UnbanMemberPermission extends BanMemberPermission {


  public function getDescription() {
    return "Permission to revoke the ban from a member.";
  }


  /**
   * @brief A moderator (or a member with a superior role) can remove a ban, but only if the member has been
   * banned by an user with an equal (or inferior) role or by himself.
   */
  public function check() {
    if (!$this->context->isBanned())
      return FALSE;
    elseif ($this->context->bannerId == $this->user->id)
      return TRUE;
    else {
      $whoBanned = $this->di['couchdb']->getDoc(Couch::STD_DOC_PATH, $this->context->bannerId);
      return $this->user->roles->isSuperior($whoBanned) ? TRUE : FALSE;
    }
  }

}