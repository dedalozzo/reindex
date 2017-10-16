<?php

/**
 * @file ImpersonatePermission.php
 * @brief This file contains the ImpersonatePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Member;


use ReIndex\Security\Permission\AbstractPermission;

use Daikengo\User\IUser;


/**
 * @brief Permission to impersonate another user: a member or a guest.
 * @details An admin can impersonate any member, but he can't impersonate another admin. A member (even an admin) can
 * impersonate a guest. No one can impersonate itself and a guest, of course, can't impersonate anyone.
 */
class ImpersonatePermission extends AbstractPermission {

  protected $someone;


  /**
   * @brief Constructor.
   * @param[in] Security::IUser $someone
   */
  public function __construct(IUser $someone) {
    $this->someone = $someone;
    parent::__construct();
  }


  /**
   * @brief A member can impersonate a guest.
   * @return bool
   */
  public function checkForMember() {
    return ($this->user->isMember() && $this->someone->isGuest()) ? TRUE : FALSE;
  }


  /**
   * @brief An admin may impersonate another member but not another admin or superuser.
   * @return bool
   */
  public function checkForAdmin() {
    if ($this->checkForMember())
      return TRUE;
    else
      // We assume someone must be a member.
      return !$this->someone->roles->areSuperiorThan(new AdminRole());
  }

}