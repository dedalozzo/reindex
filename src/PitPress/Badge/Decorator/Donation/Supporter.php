<?php

/**
 * @file Supporter.php
 * @brief This file contains the Supporter class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Donation;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Made a free donation.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Supporter extends Decorator {


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
    return ['donate'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}