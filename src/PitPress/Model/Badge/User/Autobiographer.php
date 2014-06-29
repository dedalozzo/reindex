<?php

/**
 * @file Autobiographer.php
 * @brief This file contains the Autobiographer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\User;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Completed all user profile fields.
 * @details Awarded once.
 */
class Autobiographer extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Autobiografo";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai completato in ogni sua parte il tuo profilo. Assegnato una sola volta.
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