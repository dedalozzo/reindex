<?php

/**
 * @file Tenacious.php
 * @brief This file contains the Tenacious class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Silver;


/**
 * @brief Zero score accepted answers: more than 5 and 20% of total.
 * @details Awarded once.
 */
class Tenacious extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Tenace";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai fornito più di 5 risposte con punteggio pari a 0, che rappresentano almeno il 20% delle tue risposte. Assegnato una
sola volta.
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