<?php

/**
 * @file Stellar.php
 * @brief This file contains the Stellar class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Star;


use PitPress\Model\Badge\Gold;


/**
 * @brief Wrote a post starred by 50 users.
 * @details Awarded multiple times.
 */
class Stellar extends Gold {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Stellare";
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