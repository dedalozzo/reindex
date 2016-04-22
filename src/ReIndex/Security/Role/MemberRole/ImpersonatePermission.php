<?php

/**
 * @file MemberRole/ImpersonatePermission.php
 * @brief This file contains the ImpersonatePermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the member role
namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Security\User\IUser;


/**
 * @brief Permission to impersonate another user: a member or a guest.
 * @details An admin can impersonate any member, but he can't impersonate another admin. A member (even an admin) can
 * impersonate a guest. No one can impersonate itself and a guest, of course, can't impersonate anyone.
 */
class ImpersonatePermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Security::IUser $context
   */
  public function __construct(IUser $context = NULL) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Permission to impersonate another user: a member or a guest.";
  }


  public function check() {
    return  ($this->user->isMember() && $this->context->isGuest()) ? TRUE : FALSE;
  }

}