<?php

/**
 * @file Trendy.php
 * @brief This file contains the Trendy class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Star;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Wrote a post starred by 25 users.
 * @details Awarded multiple times.
 */
class Trendy extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Trendy";
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