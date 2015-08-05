<?php

/**
 * @file Trendy.php
 * @brief This file contains the Trendy class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Star;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Wrote a post starred by 25 users.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Trendy extends Decorator {


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
    return ['star'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}