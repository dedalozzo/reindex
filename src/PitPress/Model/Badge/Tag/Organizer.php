<?php

/**
 * @file Organizer.php
 * @brief This file contains the Organizer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Tag;


use PitPress\Model\Badge\Bronze;


/**
 * @brief First retag.
 * @details Awarded once.
 */
class Organizer extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Organizzatore";
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