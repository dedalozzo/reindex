<?php

/**
 * @file Benefactor.php
 * @brief This file contains the Benefactor class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Bounty;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief First bounty manually awarded on your own question.
 * @details Awarded once.
 * @nosubgrouping
 */
class Benefactor extends Decorator {


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
    return ['bounty'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}