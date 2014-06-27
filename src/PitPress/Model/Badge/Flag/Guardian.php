<?php

/**
 * @file Guardian.php
 * @brief This file contains the Guardian class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Flag;


use PitPress\Model\Badge\Gold;


/**
 * @brief Raised 500 helpful flags.
 * @details Awarded once.
 */
class Guardian extends Gold {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Guardiano";
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