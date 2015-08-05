<?php

/**
 * @file Stellar.php
 * @brief This file contains the Stellar class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Star;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Wrote a post starred by 50 users.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Stellar extends Decorator {


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
    return ['star'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

} 