<?php

/**
 * @file Supporter.php
 * @brief This file contains the Supporter class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Donation;


use PitPress\Model\Badge\Silver;


/**
 * @brief Made a free donation.
 * @details Awarded multiple times.
 */
class Supporter extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Sostenitore";
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