<?php

/**
 * @file Student.php
 * @brief This file contains the Student class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Answer;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Asked first question with score of 1 or more.
 * @details Awarded once.
 * @nosubgrouping
 */
class Student extends Decorator {


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