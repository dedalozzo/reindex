<?php

/**
 * @file Teacher.php
 * @brief This file contains the Teacher class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Answered first question with score of 1 or more.
 * details Awarded once.
 */
class Teacher extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Insegnante";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Per la prima volta una tua risposta totalizza almeno un punto. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return Metal::BRONZE;
  }


  /**
   * @copydoc Badge::getMessages()
   */
  public function getMessages() {
    return ['vote'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}