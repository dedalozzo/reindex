<?php

/**
 * @file PhpinfoPermission.php
 * @brief This file contains the PhpinfoPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\System;


use ReIndex\Security\Permission\AbstractPermission;


/**
 * @brief Permission to display information about PHP's configuration.
 */
class PhpinfoPermission extends AbstractPermission {


  public function __construct() {
    parent::__construct();
  }


  public function getDescription() {
    return "Permission to display information about PHP's configuration.";
  }


  /**
   * @brief A developer can see the PHP's info page.
   * @return bool
   */
  public function checkForDeveloperRole() {
    return TRUE;
  }

}