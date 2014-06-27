<?php

/**
 * @file Sportsmanship.php
 * @brief This file contains the Sportsmanship class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Vote;


use PitPress\Model\Badge\Silver;


/**
 * @brief Voted 100 answers on questions where an answer of yours has a positive score.
 * @details Awarded once.
 */
class Sportsmanship extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Sportivo";
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