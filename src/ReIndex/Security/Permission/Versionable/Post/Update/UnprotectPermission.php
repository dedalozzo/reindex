<?php

/**
 * @file Update/UnprotectPermission.php
 * @brief This file contains the UnprotectPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post\Update;


use ReIndex\Security\Permission\Versionable\Post\UnprotectPermission as Superclass;



/**
 * @brief Permission to unprotect an update.
 */
abstract class UnprotectPermission extends Superclass {


  /**
   * @brief A member must be able to unprotect any post he has protected.
   */
  public function checkForMemberRole() {
    $this->checkForModeratorRole();
  }

}