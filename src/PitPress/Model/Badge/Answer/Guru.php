<?php

/**
 * @file Guru.php
 * @brief This file contains the Guru class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Silver;


/**
 * @brief Accepted answer and score of 40 or more.
 * @details Awarded multiple times.
 */
class Guru extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Guru";
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