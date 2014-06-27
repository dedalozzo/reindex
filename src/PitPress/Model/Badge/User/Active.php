<?php

/**
 * @file GitHubber.php
 * @brief This file contains the GitHubber class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\User;


use PitPress\Model\Badge\Bronze;


/**
 * @brief The user has activated his profile.
 * @details Awarded multiple times.
 */
class Active extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Attivo";
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