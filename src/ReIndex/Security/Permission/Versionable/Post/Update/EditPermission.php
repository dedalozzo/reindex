<?php

/**
 * @file Update/EditPermission.php
 * @brief This file contains the EditPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post\Update;


use ReIndex\Security\Permission\Versionable\Post\EditPermission as Superclass;


/**
 * @brief Permission to edit an update.
 */
class EditPermission extends Superclass {
  

  public function getDescription() {
    return "Permission to edit an update.";
  }

}