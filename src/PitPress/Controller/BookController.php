<?php

//! @file BookControl.php
//! @brief This file contains the BookController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use PitPress\Helper;


/**
 * @brief Controller of Book actions.
 * @nosubgrouping
 */
class BookController extends IndexController {


  protected function getLabel() {
    return 'libri';
  }


  protected function getPeriod($filter) {
    return empty($filter) ? Helper\Time::YEAR : Helper\ArrayHelper::value($filter, $this->periods);
  }


  public function popularAction($filter = NULL) {
    $this->periods = Helper\ArrayHelper::slice($this->periods, 3);
    parent::popularAction($filter);
  }

}