<?php

/**
 * @file BookController.php
 * @brief This file contains the BookController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


use ReIndex\Helper;


/**
 * @brief Controller of Book actions.
 * @nosubgrouping
 */
class BookController extends IndexController {


  /**
   * @copydoc IndexController::getLabel()
   */
  protected function getLabel() {
    return 'books';
  }


  /**
   * @copydoc BaseController::getPeriod()
   */
  protected function getPeriod($filter) {
    return empty($filter) ? Helper\Time::ALL_TIME : Helper\ArrayHelper::value($filter, $this->periods);
  }


  /**
   * @copydoc IndexController::popular()
   */
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