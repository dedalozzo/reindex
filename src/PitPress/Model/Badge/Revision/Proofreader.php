<?php

/**
 * @file Proofreader.php
 * @brief This file contains the Proofreader class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Revision;


use PitPress\Model\Badge\Silver;


/**
 * @brief Approved or rejected 100 suggested edits.
 * @details Awarded once.
 */
class Proofreader extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Correttore";
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