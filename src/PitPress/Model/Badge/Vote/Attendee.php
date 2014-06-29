<?php

/**
 * @file Voter.php
 * @brief This file contains the Voter class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Vote;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Voted 10 times in a day.
 * @details Awarded once.
 */
class Attendee extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Partecipante";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai votato 10 volte in un giorno. Assegnato una sola volta.
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