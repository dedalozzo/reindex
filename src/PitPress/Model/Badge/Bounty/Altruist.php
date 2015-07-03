<?php

/**
 * @file Altruist.php
 * @brief This file contains the Altruist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Bounty;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief First bounty manually awarded on another person's question.
 * @details Awarded once.
 */
class Altruist extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Altruista";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai assegnato manualmente il tuo primo bounty alla domanda di un altro utente. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return Metal::BRONZE;
  }


  /**
   * @copydoc Badge::getMessages()
   */
  public function getMessages() {
    return ['bounty'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}