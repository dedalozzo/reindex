<?php

/**
 * @file Enthusiast.php
 * @brief This file contains the Enthusiast class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Visit;


use PitPress\Model\Badge\Silver;


/**
 * @brief Visited the site each day for 30 consecutive days.
 * @details Awarded once.
 */
class Enthusiast extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Entusiasta";
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