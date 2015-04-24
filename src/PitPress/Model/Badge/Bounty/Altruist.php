<?php

/**
 * @file Altruist.php
 * @brief This file contains the Altruist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Bounty;


use PitPress\Model\Badge\Bronze;


/**
 * @brief First bounty manually awarded on another person's question.
 * @details Awarded once.
 */
class Altruist extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Altruista";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai assegnato manualmente il tuo primo bounty alla domanda di un altro utente. Assegnato una sola volta.
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