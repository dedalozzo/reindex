<?php

/**
 * @file Stellar.php
 * @brief This file contains the Stellar class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Star;


use PitPress\Model\Badge\Gold;


/**
 * @brief Wrote a post starred by 50 users.
 * @details Awarded multiple times.
 */
class Stellar extends Gold {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Stellare";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return "Primo a rispondere ad una domanda; risposta accetatta con punteggio di 10.";
  }


  /**
   * @copydoc Badge::getDetails()
   */
  public function getDetails() {
    return <<<'DESC'
Sei la prima persona a rispondere ad una domanda e totalizzare un punteggio pari a 10 per la risposta. Quest'ultima deve
essere accettata dall'autore della domanda. Il badge viene assegnato soltanto nel caso in cui l'autore della domanda non
risponda alla sua stessa domanda. Assegnato più volte.
DESC;
  }


  /**
   * @copydoc Badge::award()
   */
  public function award() {

  }


  /**
   * @copydoc Badge::withdrawn()
   */
  public function withdrawn() {

  }

} 