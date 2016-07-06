<?php

/**
 * @file UpdateController.php
 * @brief This file contains the UpdateController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


use ReIndex\Helper;


/**
 * @brief Controller of Update actions.
 * @nosubgrouping
 */
final class UpdateController extends IndexController {


  /**
   * @copydoc IndexController::getLabel()
   */
  protected function getLabel() {
    return 'links';
  }


  /**
   * @brief Creates a new link.
   */
  public function newAction() {
    parent::newAction();
  }

}