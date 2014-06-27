<?php

/**
 * @file Great.php
 * @brief This file contains the Great class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Score;


use PitPress\Model\Badge\Gold;


/**
 * @brief Wrote a post with 50 score.
 * @details Awarded multiple times.
 */
class Awesome extends Gold {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Grandioso";
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