<?php

/**
 * @file Resurrectionist.php
 * @brief This file contains the Resurrectionist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Answered more than 30 days later as first answer scoring 2 or more.
 * @details Awarded multiple times.
 */
class Resurrectionist extends Bronze {


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
    return "Primo a rispondere ad una domanda.";
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