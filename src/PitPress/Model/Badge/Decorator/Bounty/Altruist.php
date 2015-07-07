<?php

/**
 * @file Altruist.php
 * @brief This file contains the Altruist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Bounty;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief First bounty manually awarded on another person's question.
 * @details Awarded once.
 * @nosubgrouping
 */
class Altruist extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Altruista";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai assegnato manualmente il tuo primo bounty alla domanda di un altro utente. Assegnato una sola volta.
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