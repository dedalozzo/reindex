<?php

/**
 * @file Autobiographer.php
 * @brief This file contains the Autobiographer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\User;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Completed all user profile fields.
 * @details Awarded once.
 * @nosubgrouping
 */
class Autobiographer extends Decorator {


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
    return ['completed profile'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

} 