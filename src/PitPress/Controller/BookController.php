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
    return empty($filter) ? Helper\Time::EVER : Helper\ArrayHelper::value($filter, $this->periods);
  }


  protected function popular($filter, $unversionTagId = NULL) {
    $this->periods = Helper\ArrayHelper::slice($this->periods, 3);
    parent::popular($filter);
  }


  /**
   * @brief Creates a new book.
   */
  public function newAction() {
    parent::newAction();
  }

}