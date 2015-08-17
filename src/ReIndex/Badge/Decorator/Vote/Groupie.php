<?php

/**
 * @file Groupie.php
 * @brief This file contains the Groupie class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Badge\Decorator\Vote;


use ReIndex\Enum\Metal;


/**
 * @brief Voted at least 500 times.
 * @details Awarded once.
 * @nosubgrouping
 */
class Groupie extends Fan {


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
    return ['vote'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

} 