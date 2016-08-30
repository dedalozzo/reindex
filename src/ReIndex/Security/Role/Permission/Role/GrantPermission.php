<?php

/**
 * @file GuestRole/GrantRolePermission.php
 * @brief This file contains the GrantRolePermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the admin role
namespace ReIndex\Security\Role\GuestRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Security\Role\IRole;
use ReIndex\Security\Role\MemberRole;


/**
 * @brief Permission to grant or revoke a role.
 */
class GrantRolePermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] IRole $context
   */
  public function __construct(IRole $context = NULL) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Permission to grant a role to a member or revoke it.";
  }

}


$checkForGuest = function($context) {
  // A member can assign to himself only the MemberRole. This is done since the member role is assigned.
  return ($context instanceof MemberRole) ? TRUE : FALSE;
};

$checkForAdmin = function($context) {
  return $this->user->roles->areSuperiorThan($context);
};