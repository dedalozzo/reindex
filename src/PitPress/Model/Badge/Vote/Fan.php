<?php

/**
 * @file Fan.php
 * @brief This file contains the Fan class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Vote;


use PitPress\Model\Badge\Silver;


/**
 * @brief Voted 250 or more times.
 * @details Awarded once.
 */
class Fan extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Fan";
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