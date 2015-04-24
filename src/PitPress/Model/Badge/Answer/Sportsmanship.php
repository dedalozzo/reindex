<?php

/**
 * @file Sportsmanship.php
 * @brief This file contains the Sportsmanship class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Silver;


/**
 * @brief Voted 100 answers on questions where an answer of yours has a positive score.
 * @details Awarded once.
 */
class Sportsmanship extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Sportivo";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai votato 100 risposte su domande dove una delle tue risposte ha almeno un punto. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Badge::exist()
   */
  public function exist() {

  }


  /**
   * @copydoc Badge::deserve()
   */
  public function deserve() {

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