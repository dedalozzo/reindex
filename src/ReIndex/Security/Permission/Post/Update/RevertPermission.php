<?php

/**
 * @file Update/RevertPermission.php
 * @brief This file contains the RevertPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Update;


/**
 * @brief Permission to revert the content to a specific revision.
 */
class RevertPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to revert the update content to a specific revision.";
  }


  public function checkForModeratorRole() {
    return TRUE;
  }

}