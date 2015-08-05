<?php

/**
 * @file Epic.php
 * @brief This file contains the Epic class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Reputation;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Earned 200 daily reputation 50 times.
 * @details Awarded once.
 * @nosubgrouping
 */
class Epic extends Decorator {


  /**
   * @copydoc Decorator::getMetal()
   */
  public function getMetal() {
    return Metal::SILVER;
  }


  /**
   * @copydoc IObserver::getMessages()
   */
  public function getMessages() {
    return ['reputation'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}