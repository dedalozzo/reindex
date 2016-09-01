<?php

/**
 * @file Article/RevertPermission.php
 * @brief This file contains the RevertPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Article;


/**
 * @brief Permission to revert the content to a specific revision.
 */
class RevertToVersionPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to revert the article content to a specific revision.";
  }


  public function checkForModeratorRole() {
    return TRUE;
  }

}