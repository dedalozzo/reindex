<?php

/**
 * @file Philanthropist.php
 * @brief This file contains the Philanthropist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Score;


use PitPress\Model\Badge\Gold;


/**
 * @brief Wrote a post with 100 score.
 * @details Awarded multiple times.
 */
class Amazing extends Gold {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Formidabile";
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