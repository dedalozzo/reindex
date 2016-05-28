<?php

/**
 * @file AdminRole/GrantRolePermission.php
 * @brief This file contains the GrantRolePermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the admin role
namespace ReIndex\Security\Role\AdminRole;


use ReIndex\Security\Role\GuestRole\GrantRolePermission as Superclass;


/**
 * @brief Permission to grant or revoke a role.
 */
class GrantRolePermission extends Superclass {


  public function check() {
    return $this->user->roles->areSuperiorThan($this->context);
  }

}