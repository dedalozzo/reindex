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
 * @brief Permission to grant a role to a member.
 */
class GrantRolePermission extends AbstractPermission {

  public $role;


  public function __construct(IRole $role) {
    parent::__construct();
    $this->role = $role;
  }


  public function getDescription() {
    return "Permission to grant a role to a member.";
  }


  public function check() {
    return !$this->user->roles->isSuperior($this->role);
  }

}