<?php

/*
 * @file Enlightened.php
 * @brief This file contains the Enlightened class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Answer;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief First to answer and accepted with score of 10 or more.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Enlightened extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Illuminato";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Sei la prima persona a rispondere ad una domanda e totalizzare un punteggio pari a 10 per la risposta. Quest'ultima deve
essere accettata dall'autore della domanda. Il badge viene assegnato soltanto nel caso in cui l'autore della domanda non
risponda alla sua stessa domanda. Assegnato più volte.
DESC;
  }


  /**
   * @copydoc Decorator::getMetal()
   */
  public function getMetal() {
    return Metal::SILVER;
  }


  /**
   * @copydoc IObserver::getMessages()
   */
  public function getMessages() {
    return ['vote, accept'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}