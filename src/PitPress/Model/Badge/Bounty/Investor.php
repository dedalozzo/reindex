<?php

/**
 * @file Investor.php
 * @brief This file contains the Investor class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Bounty;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief First bounty offered on another user's question.
 * @details Awarded once.
 */
class Investor extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Investitore";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai offerto il tuo primo bounty sulla domanda di un altro utente. Assegnato una sola volta.
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