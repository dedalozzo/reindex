<?php

/**
 * @file Resurrectionist.php
 * @brief This file contains the Resurrectionist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Answered more than 30 days later as first answer scoring 2 or more.
 * @details Awarded multiple times.
 */
class Resurrectionist extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Risurrezionista";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai risposto, per primo, ad una domanda formulata almeno un mese prima, e la tua risposta ha totalizzato almeno 2 punti.
Assegnato più volte.
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
    return ['vote'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}