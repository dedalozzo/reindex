<?php

/**
 * @file Fan.php
 * @brief This file contains the Fan class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Badge\Decorator\Vote;


use ReIndex\Enum\Metal;


/**
 * @brief Voted 250 or more times.
 * @details Awarded once.
 * @nosubgrouping
 */
class Fan extends Attendee {


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
    return ['vote'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

} 