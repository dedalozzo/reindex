<?php

/**
 * @file Fanatic.php
 * @brief This file contains the Fanatic class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Visit;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Visited the site each day for 100 consecutive days.
 * @details Awarded once.
 * @nosubgrouping
 */
class Fanatic extends Decorator {



  /**
   * @copydoc Decorator::getMetal()
   */
  public function getMetal() {
    return Metal::GOLD;
  }


  /**
   * @copydoc IObserver::getMessages()
   */
  public function getMessages() {
    return ['time'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

} 