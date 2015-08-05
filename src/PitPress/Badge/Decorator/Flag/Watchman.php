<?php

/**
 * @file Watchman.php
 * @brief This file contains the Watchman class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Flag;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief First flagged post.
 * @details Awarded once.
 * @nosubgrouping
 */
class Watchman extends Decorator {


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
    return ['flag'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

} 