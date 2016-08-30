<?php

/**
 * @file DebugPermission.php
 * @brief This file contains the DebugPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! System's permissions
namespace ReIndex\Security\Role\Permission\System;


use ReIndex\Security\Role\Permission\AbstractPermission;


/**
 * @brief Permission to display information related an internal server error, in case the system throw an exception.
 */
class DebugPermission extends AbstractPermission {


  public function __construct() {
    parent::__construct();
  }


  public function getDescription() {
    return "The member can use the integrated debugger.";
  }


  /**
   * @brief A developer can display useful information in case of exception.
   * @return bool
   */
  public function checkForDeveloperRole() {
    return TRUE;
  }

}