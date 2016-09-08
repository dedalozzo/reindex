<?php

/**
 * @file Question/ViewPermission.php
 * @brief This file contains the ViewPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post\Question;


use ReIndex\Security\Permission\Versionable\Post\ViewPermission as Superclass;


/**
 * @brief Permission to display an question.
 */
class ViewPermission extends Superclass {


  public function getDescription() {
    return "Permission to read an question.";
  }

}