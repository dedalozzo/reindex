<?php

/**
 * @file Linguist.php
 * @brief This file contains the Linguist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Tag;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief First approved tag synonym.
 * @details Awarded once.
 * @nosubgrouping
 */
class Linguist extends Decorator {


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
    return ['approve synonym'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

} 