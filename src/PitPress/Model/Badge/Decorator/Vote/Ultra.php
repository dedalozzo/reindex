<?php

/**
 * @file Ultra.php
 * @brief This file contains the Ultra class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Vote;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Used the maximum 50 votes in a day.
 * @details Awarded once.
 * @nosubgrouping
 */
class Ultra extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Ultrà";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai usato il massimo di 50 voti in un giorno. Assegnato una sola volta.
DESC;
  }


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
    return ['vote'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

} 