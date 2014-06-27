<?php

/**
 * @file Great.php
 * @brief This file contains the Great class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Reputation;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Earned at least 200 reputation in a single day
 * @details Awarded once.
 */
class Great extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Grande";
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