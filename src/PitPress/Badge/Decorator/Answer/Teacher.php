<?php

/**
 * @file Teacher.php
 * @brief This file contains the Teacher class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Answer;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Answered first question with score of 1 or more.
 * details Awarded once.
 * @nosubgrouping
 */
class Teacher extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Insegnante";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Per la prima volta una tua risposta totalizza almeno un punto. Assegnato una sola volta.
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
    return ['vote'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}