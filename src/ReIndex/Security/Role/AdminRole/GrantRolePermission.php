<?php

/**
 * @file GrantRolePermission.php
 * @brief This file contains the GrantRolePermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the admin role
namespace ReIndex\Security\Role\AdminRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Security\Role\IRole;


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
    return $this->user->roles->areSuperiorThan($this->context);
  }

}