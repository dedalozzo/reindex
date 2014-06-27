<?php

/**
 * @file Archaeologist.php
 * @brief This file contains the Archaeologist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Revision;


use PitPress\Model\Badge\Silver;


/**
 * @brief Edited 100 posts that were inactive for 6 months.
 * @details Awarded once.
 */
class Archaeologist extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Archeologo";
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