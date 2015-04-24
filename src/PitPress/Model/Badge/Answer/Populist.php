<?php

/**
 * @file Populist.php
 * @brief This file contains the Populist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Gold;


/**
 * @brief Highest scoring answer that outscored an accepted answer with score of more than 10 by more than twice.
 * @details Awarded multiple times.
 */
class Populist extends Gold {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Populista";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai fornito una risposta ad una domanda per la quale già esiste una risposta accettata con un punteggio pari o superiore
a 10, e la tua risposta ha ottenuto almeno il doppio dei punti. Assegnato più volte.
DESC;
  }


  /**
   * @copydoc Badge::exist()
   */
  public function exist() {

  }


  /**
   * @copydoc Badge::deserve()
   */
  public function deserve() {

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