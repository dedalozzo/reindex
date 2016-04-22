<?php

/**
 * @file RevertToVersionPermission.php
 * @brief This file contains the RevertToVersionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\AbstractPermission;


/**
 * @brief Permission to revert the content to a specific revision.
 */
class RevertToVersionPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to revert the content to a specific revision.";
  }


  public function check() {
    return TRUE;
  }

}