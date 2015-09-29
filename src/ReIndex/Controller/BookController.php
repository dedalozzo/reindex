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
   * @copydoc IndexController::popular()
   */
  protected function popular($filter, $unversionTagId = NULL) {
    $this->periods = Helper\ArrayHelper::slice($this->periods, 3);
    parent::popular($filter, $unversionTagId);
  }


  /**
   * @brief Creates a new book.
   */
  public function newAction() {
    parent::newAction();
  }

}