<?php

/**
 * @file GrantPermission.php
 * @brief This file contains the GrantPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions related to the roles
namespace ReIndex\Security\Permission\Role;


use ReIndex\Security\Permission\AbstractPermission;

use Daikengo\Role\IRole;
use Daikengo\Role\GuestRole;


/**
 * @brief Permission to grant a role.
 */
class GrantPermission extends AbstractPermission {

  protected $role;


  /**
   * @brief Constructor.
   * @param[in] IRole $role
   */
  public function __construct(IRole $role) {
    $this->role = $role;
    parent::__construct();
  }


  public function getDescription() {
    return "Permission to grant a role to a member.";
  }


  /**
   * @brief A guest can assign to a newly created member the `MemberRole`.
   * @details This is done since every member must have such a role.
   * @return bool
   */
  public function checkForGuestRole() {
    return ($this->role instanceof GuestRole) ? TRUE : FALSE;
  }


  /**
   * @brief An admin can associate every role but not the `SupervisorRole`.
   * @return bool
   */
  public function checkForAdminRole() {
    return $this->user->roles->areSuperiorThan($this->role);
  }

}