<?php

/**
 * @file Moderator.php
 * @brief This file contains the Moderator class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Revision;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Served as a moderator for at least 1 year.
 * @details Awarded once.
 * @nosubgrouping
 */
class Moderator extends Decorator {


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