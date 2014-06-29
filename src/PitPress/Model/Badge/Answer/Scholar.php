<?php

/**
 * @file Scholar.php
 * @brief This file contains the Scholar class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Asked a question and accepted an answer.
 * @details Awarded once.
 */
class Scholar extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Scolaro";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai formulato la tua prima domanda e hai accettato una risposta. Assegnato una sola volta.
DESC;
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