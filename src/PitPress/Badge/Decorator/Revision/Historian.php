<?php

/**
 * @file Historian.php
 * @brief This file contains the Historian class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Revision;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Edited first post that was inactive for 6 months.
 * @details Awarded once.
 * @nosubgrouping
 */
class Historian extends Decorator {


  /**
   * @copydoc Decorator::getMetal()
   */
  public function getMetal() {
    return Metal::BRONZE;
  }


  /**
   * @copydoc IObserver::getMessages()
   */
  public function getMessages() {
    return ['edit'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}