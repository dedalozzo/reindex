<?php

/**
 * @file Watchman.php
 * @brief This file contains the Watchman class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Flag;


use PitPress\Model\Badge\Bronze;


/**
 * @brief First flagged post.
 * @details Awarded once.
 */
class Watchman extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Vigilante";
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