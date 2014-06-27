<?php

/**
 * @file Moderator.php
 * @brief This file contains the Moderator class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Revision;


use PitPress\Model\Badge\Gold;


/**
 * @brief Served as a moderator for at least 1 year.
 * @details Awarded once.
 */
class Moderator extends Gold {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Moderatore";
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