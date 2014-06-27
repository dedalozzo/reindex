<?php

/**
 * @file SelfLearner.php
 * @brief This file contains the SelfLearner class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Answered your own question with score of 3 or more.
 * @details Awarded once.
 */
class SelfLearner extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Autodidatta";
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