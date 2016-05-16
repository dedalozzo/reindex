<?php

/**
 * @file GrantRolePermission.php
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


  public function check() {
    // A member can assign to himself only the MemberRole. This is done since the member role is assigned.
    return ($this->context instanceof MemberRole) ? TRUE : FALSE;
  }

}