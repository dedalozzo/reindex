<?php

/**
 * @file Promoter.php
 * @brief This file contains the Promoter class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Bounty;


use PitPress\Model\Badge\Bronze;


/**
 * @brief First bounty offered on your own question.
 * @details Awarded once.
 */
class Promoter extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Promotore";
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