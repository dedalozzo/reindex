<?php

//! @file ArticleControl.php
//! @brief This file contains the ArticleController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use PitPress\Helper;


/**
 * @brief Controller of Article actions.
 * @nosubgrouping
 */
class ArticleController extends IndexController {


  protected function getLabel() {
    return 'articoli';
  }


  protected function getPeriod($filter) {
    return empty($filter) ? Helper\Time::EVER : Helper\ArrayHelper::value($filter, $this->periods);
  }


  public function popularAction($filter = NULL) {
    $this->periods = Helper\ArrayHelper::slice($this->periods, 5);
    parent::popularAction($filter);
  }

} 