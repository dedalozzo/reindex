<?php

//! @file LinkGroup.php
//! @brief This file contains the LinkGroup class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


use Phalcon\DI;


/**
 * @brief Group of Link routes.
 * @nosubgrouping
 */
class LinkGroup extends IndexGroup {


  protected function getController() {
    return 'book';
  }


  protected function getDefaultAction() {
    return 'newest';
  }


  public function getPrefix() {
    return '/links';
  }

} 