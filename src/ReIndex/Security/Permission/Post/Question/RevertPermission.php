<?php

/**
 * @file Question/RevertPermission.php
 * @brief This file contains the RevertPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Question;


/**
 * @brief Permission to revert the content to a specific revision.
 */
class RevertToVersionPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to revert the question content to a specific revision.";
  }


  public function checkForModeratorRole() {
    return TRUE;
  }

}