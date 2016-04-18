<?php

/**
 * @file RevokeRolePermission.php
 * @brief This file contains the RevokeRolePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Admin;


/**
 * @brief Permission to revoke a role from a member.
 */
class RevokeRolePermission extends GrantRolePermission {


  public function getDescription() {
    return "Permission to revoke a role from a member.";
  }

}