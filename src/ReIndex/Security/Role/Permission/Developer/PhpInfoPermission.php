<?php

/**
 * @file PhpinfoPermission.php
 * @brief This file contains the PhpinfoPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Developer;


use ReIndex\Security\Role\Permission\AbstractPermission;


/**
 * @brief Permission to display information about PHP's configuration.
 */
class PhpinfoPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to display information about PHP's configuration.";
  }


  public function check() {
    return TRUE;
  }

}