<?php

/**
 * @file Necromancer.php
 * @brief This file contains the Necromancer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Answer;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Answered a question more than 60 days older with score of 5 or more.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Necromancer extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Negromante";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai risposta ad una domanda formulata almeno 2 mesi prima; la tua risposta ha ottenuto almeno 5 punti. Assegnato più
volte.
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
    return ['vote'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}