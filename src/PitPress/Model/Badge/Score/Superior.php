<?php

/**
 * @file Superior.php
 * @brief This file contains the Superior class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Score;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Wrote a post with 30 score.
 * @details Awarded multiple times.
 */
class Superior extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Superiore";
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