<?php

/**
 * @file Update/ViewPermission.php
 * @brief This file contains the ViewPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post\Update;


use ReIndex\Security\Permission\Versionable\Post\ViewPermission as Superclass;


/**
 * @brief Permission to display an update.
 */
class ViewPermission extends Superclass  {


  public function getDescription() {
    return "Permission to read an update.";
  }

}