<?php

/**
 * @file RevokePermission.php
 * @brief This file contains the RevokePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Role;


/**
 * @brief Permission to revoke a role.
 */
class RevokePermission extends GrantPermission {

  protected $role;


  public function getDescription() {
    return "Permission to revoke a role from a member.";
  }

}