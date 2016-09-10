<?php

/**
 * @file Update/UnprotectPermission.php
 * @brief This file contains the UnprotectPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Revision\Post\Update;


use ReIndex\Security\Permission\Revision\Post\UnprotectPermission as Superclass;


/**
 * @brief Permission to unprotect an update.
 */
class UnprotectPermission extends Superclass {


  /**
   * @brief A member must be able to unprotect any update he has protected.
   */
  public function checkForMemberRole() {
    $this->checkForModeratorRole();
  }

}