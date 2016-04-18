<?php
/**
 * @file DebugPermission.php
 * @brief This file contains the DebugPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the developer role
namespace ReIndex\Security\Role\Permission\Developer;


use ReIndex\Security\Role\Permission\AbstractPermission;


/**
 * @brief Permission to use the integrated debugger.
 */
class DebugPermission extends AbstractPermission {


  public function getDescription() {
    return "The member can use the integrated debugger.";
  }


  public function check() {
    return TRUE;
  }

}