<?php

/**
 * @file Reporter.php
 * @brief This file contains the Reporter class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Post;


use PitPress\Model\Badge\Silver;


/**
 * @brief Reported 25 links.
 * @details Awarded once.
 */
class Reporter extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Corrispondente";
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