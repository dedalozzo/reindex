<?php

/**
 * @file Benefactor.php
 * @brief This file contains the Benefactor class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Bounty;


use PitPress\Model\Badge\Bronze;


/**
 * @brief First bounty manually awarded on your own question.
 * @details Awarded once.
 */
class Benefactor extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Benefattore";
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