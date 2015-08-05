<?php

/**
 * @file Doorman.php
 * @brief This file contains the Doorman class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Flag;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Raised 50 helpful flags.
 * @details Awarded once.
 * @nosubgrouping
 */
class Doorman extends Decorator {


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
    return ['flag'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}