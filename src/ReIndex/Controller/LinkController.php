<?php

/**
 * @file LinkController.php
 * @brief This file contains the LinkController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


use ReIndex\Helper;


/**
 * @brief Controller of Link actions.
 * @nosubgrouping
 */
class LinkController extends IndexController {


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