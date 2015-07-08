<?php

/**
 * @file Investor.php
 * @brief This file contains the Investor class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Bounty;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief First bounty offered on another user's question.
 * @details Awarded once.
 * @nosubgrouping
 */
class Investor extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Investitore";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai offerto il tuo primo bounty sulla domanda di un altro utente. Assegnato una sola volta.
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
    return ['bounty'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

} 