<?php

/**
 * @file Teacher.php
 * @brief This file contains the Teacher class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Answered first question with score of 1 or more.
 * details Awarded once.
 */
class Teacher extends Bronze {


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
    return "Primo a rispondere ad una domanda.";
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