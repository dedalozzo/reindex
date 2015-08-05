<?php

/**
 * @file Ultra.php
 * @brief This file contains the Ultra class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Vote;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Used the maximum 50 votes in a day.
 * @details Awarded once.
 * @nosubgrouping
 */
class Ultra extends Decorator {


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
    return ['vote'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

} 