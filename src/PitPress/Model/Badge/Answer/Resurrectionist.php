<?php

/**
 * @file Resurrectionist.php
 * @brief This file contains the Resurrectionist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Answered more than 30 days later as first answer scoring 2 or more.
 * @details Awarded multiple times.
 */
class Resurrectionist extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Risurrezionista";
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