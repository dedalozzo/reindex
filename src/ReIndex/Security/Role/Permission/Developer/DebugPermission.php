<?php
/**
 * @file DebuggingPermission.php
 * @brief This file contains the DebuggingPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the developer role
namespace ReIndex\Security\Role\Permission\Developer;


use ReIndex\Security\Role\Permission\AbstractPermission;


/**
 * @brief Permission to use the integrated debugger.
 */
class DebuggingPermission extends AbstractPermission {


  public function getDescription() {
    return "The member can use the integrated debugger.";
  }


  public function check() {
    return TRUE;
  }

}