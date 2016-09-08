<?php

/**
 * @file Update/EditPermission.php
 * @brief This file contains the EditPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Update;


use ReIndex\Security\Permission\Post\EditPermission as Superclass;


/**
 * @brief Permission to edit an update.
 */
class EditPermission extends Superclass {
  

  public function getDescription() {
    return "Permission to edit an update.";
  }

}