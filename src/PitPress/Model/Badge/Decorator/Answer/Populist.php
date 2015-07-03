<?php

/**
 * @file Populist.php
 * @brief This file contains the Populist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Highest scoring answer that outscored an accepted answer with score of more than 10 by more than twice.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Populist extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Populista";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai fornito una risposta ad una domanda per la quale già esiste una risposta accettata con un punteggio pari o superiore
a 10, e la tua risposta ha ottenuto almeno il doppio dei punti. Assegnato più volte.
DESC;
  }


  /**
   * @copydoc Decorator::getMetal()
   */
  public function getMetal() {
    return Metal::GOLD;
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
  public function update() {

  }

}