<?php

/**
 * @file Fanatic.php
 * @brief This file contains the Fanatic class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Visit;


use PitPress\Model\Badge\Gold;


/**
 * @brief Visited the site each day for 100 consecutive days.
 * @details Awarded once.
 */
class Fanatic extends Gold {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Fanatico";
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