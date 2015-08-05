<?php

/**
 * @file Philanthropist.php
 * @brief This file contains the Philanthropist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Donation;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Made a donation of at least 100 €.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Philanthropist extends Decorator {


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
    return ['donate'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}