<?php

/**
 * @file Excellent.php
 * @brief This file contains the Excellent class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Score;


use PitPress\Model\Badge\Silver;


/**
 * @brief Wrote a post with 15 score.
 * @details Awarded multiple times.
 */
class Excellent extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Eccellente";
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