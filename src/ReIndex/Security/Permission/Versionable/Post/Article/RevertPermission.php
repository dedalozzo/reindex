<?php

/**
 * @file Article/RevertPermission.php
 * @brief This file contains the RevertPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post\Article;


/**
 * @brief Permission to revert the content to a specific revision.
 */
class RevertPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to revert the article content to a specific revision.";
  }


  public function checkForModeratorRole() {
    return TRUE;
  }

}