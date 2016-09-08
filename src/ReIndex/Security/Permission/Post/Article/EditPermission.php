<?php

/**
 * @file Article/EditPermission.php
 * @brief This file contains the EditPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Article;


use ReIndex\Security\Permission\Post\EditPermission as Superclass;


/**
 * @brief Permission to edit an article.
 */
class EditPermission extends Superclass {
  

  public function getDescription() {
    return "Permission to edit an article.";
  }

}