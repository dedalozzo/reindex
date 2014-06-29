<?php

/**
 * @file Legendary.php
 * @brief This file contains the Legendary class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Reputation;


use PitPress\Model\Badge\Gold;


/**
 * @brief Earned 200 daily reputation 150 times.
 * @details Awarded once.
 */
class Legendary extends Gold {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Leggendario";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai ottenuto 200 punti di reputazione per 150 volte. Assegnato una sola volta.
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