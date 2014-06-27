<?php

/**
 * @file Voter.php
 * @brief This file contains the Voter class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Vote;


use PitPress\Model\Badge\Bronze;


/**
 * @brief First vote.
 * @details Awarded once.
 */
class Voter extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Votante";
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