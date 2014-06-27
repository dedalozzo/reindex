<?php

/**
 * @file Tenacious.php
 * @brief This file contains the Tenacious class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Silver;


/**
 * @brief Zero score accepted answers: more than 5 and 20% of total.
 * @details Awarded once.
 */
class Tenacious extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Tenace";
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