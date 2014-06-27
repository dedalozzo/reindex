<?php

/**
 * @file Student.php
 * @brief This file contains the Student class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Asked first question with score of 1 or more.
 * @details Awarded once.
 */
class Student extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Studente";
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