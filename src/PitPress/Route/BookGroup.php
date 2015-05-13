<?php

/**
 * @file BookGroup.php
 * @brief This file contains the BookGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Route;


use Phalcon\DI;


/**
 * @brief Group of Book routes.
 * @nosubgrouping
 */
class BookGroup extends IndexGroup {


  protected function getController() {
    return 'book';
  }


  protected function getDefaultAction() {
    return 'newest';
  }


  public function getPrefix() {
    return '/libri';
  }

} 