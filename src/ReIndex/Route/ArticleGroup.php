<?php

/**
 * @file ArticleGroup.php
 * @brief This file contains the ArticleGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Route;


use Phalcon\Di;


/**
 * @brief Group of Article routes.
 * @nosubgrouping
 */
class ArticleGroup extends IndexGroup {


  protected function getController() {
    return 'article';
  }


  protected function getDefaultAction() {
    return 'newest';
  }


  public function getPrefix() {
    return '/articles';
  }

} 