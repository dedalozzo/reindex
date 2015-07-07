<?php

/**
 * @file SelfLearner.php
 * @brief This file contains the SelfLearner class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Answered your own question with score of 3 or more.
 * @details Awarded once.
 * @nosubgrouping
 */
class SelfLearner extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Autodidatta";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai risposto ad una tua stessa domanda e la tua risposta ha totalizzato almeno 3 punti. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Decorator::getMetal()
   */
  public function getMetal() {
    return Metal::BRONZE;
  }


  /**
   * @copydoc IObserver::getMessages()
   */
  public function getMessages() {
    return ['score'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}