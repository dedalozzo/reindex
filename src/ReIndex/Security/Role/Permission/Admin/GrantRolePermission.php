<?php
/**
 * @file GrantRolePermission.php
 * @brief This file contains the GrantRolePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Admin;


use ReIndex\Security\Role\Permission\AbstractPermission;
use ReIndex\Security\Role\IRole;


/**
 * @brief Permission to grant or revoke a role.
 */
class GrantRolePermission extends AbstractPermission {

  public $role;


  public function __construct(IRole $role) {
    parent::__construct();
    $this->role = $role;
  }


  public function getDescription() {
    return "Permission to grant a role to a member or revoke it.";
  }


  public function check() {
    return !$this->user->roles->isSuperior($this->role);
  }

}