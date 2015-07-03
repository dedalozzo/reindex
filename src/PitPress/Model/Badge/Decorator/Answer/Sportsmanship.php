<?php

/**
 * @file Sportsmanship.php
 * @brief This file contains the Sportsmanship class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Voted 100 answers on questions where an answer of yours has a positive score.
 * @details Awarded once.
 * @nosubgrouping
 */
class Sportsmanship extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Sportivo";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai votato 100 risposte su domande dove una delle tue risposte ha almeno un punto. Assegnato una sola volta.
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
  public function update() {

  }

}