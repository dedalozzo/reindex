<?php

/**
 * @file Scholar.php
 * @brief This file contains the Scholar class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Asked a question and accepted an answer.
 * @details Awarded once.
 * @nosubgrouping
 */
class Scholar extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Scolaro";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai formulato la tua prima domanda e hai accettato una risposta. Assegnato una sola volta.
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
    return ['accept'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}