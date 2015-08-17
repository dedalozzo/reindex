<?php

/**
 * @file Fanatic.php
 * @brief This file contains the Fanatic class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Badge\Decorator\Visit;


use ReIndex\Badge\Decorator\Decorator;
use ReIndex\Enum\Metal;


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