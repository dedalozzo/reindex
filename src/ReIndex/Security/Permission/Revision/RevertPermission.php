<?php

/**
 * @file Revision/RevertPermission.php
 * @brief This file contains the RevertPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Revision;


/**
 * @brief Permission to revert the content to a specific revision.
 */
class RevertPermission extends AbstractPermission {


  public function checkForModeratorRole() {
    return TRUE;
  }

}