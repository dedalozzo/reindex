<?php

/**
 * @file Pundit.php
 * @brief This file contains the Pundit class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Reply;


use PitPress\Model\Badge\Silver;


/**
 * @brief Left 10 comments with score of 5 or more.
 * @details Awarded once.
 */
class Pundit extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Dotto";
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