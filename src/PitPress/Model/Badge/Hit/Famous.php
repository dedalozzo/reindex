<?php

/**
 * @file Famous.php
 * @brief This file contains the Famous class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Hit;


use PitPress\Model\Badge\Gold;


/**
 * @brief Wrote a post with 20.000 views.
 * @details Awarded multiple times.
 */
class Famous extends Gold {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Famoso";
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