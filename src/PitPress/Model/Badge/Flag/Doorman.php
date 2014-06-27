<?php

/**
 * @file Doorman.php
 * @brief This file contains the Doorman class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Flag;


use PitPress\Model\Badge\Silver;


/**
 * @brief Raised 80 helpful flags.
 * @details Awarded once.
 */
class Doorman extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Portinaio";
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