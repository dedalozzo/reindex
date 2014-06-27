<?php

/**
 * @file Ultra.php
 * @brief This file contains the Ultra class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Vote;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Used the maximum 50 votes in a day.
 * @details Awarded once.
 */
class Ultra extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Ultrà";
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