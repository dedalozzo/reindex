<?php

/**
 * @file DebugPermission.php
 * @brief This file contains the DebugPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the developer role
namespace ReIndex\Security\Role\DeveloperRole;


use ReIndex\Security\Role\AbstractPermission;


/**
 * @brief Permission to display information related an internal server error, in case the system throw an exception.
 */
class DebugPermission extends AbstractPermission {


  public function getDescription() {
    return "The member can use the integrated debugger.";
  }


  public function check() {
    return TRUE;
  }

}