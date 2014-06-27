<?php

/**
 * @file Beloved.php
 * @brief This file contains the Beloved class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Star;


use PitPress\Model\Badge\Silver;


/**
 * @brief Wrote a post starred by 10 users.
 * @details Awarded multiple times.
 */
class Beloved extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Popolare";
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