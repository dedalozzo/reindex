<?php

/**
 * @file UpdateGroup.php
 * @brief This file contains the UpdateGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Route;


use Phalcon\Di;


/**
 * @brief Group of Update routes.
 * @nosubgrouping
 */
class UpdateGroup extends IndexGroup {


  protected function getController() {
    return 'update';
  }


  protected function getDefaultAction() {
    return 'newest';
  }


  public function getPrefix() {
    return '/updates';
  }

}