<?php

/**
 * @file Epic.php
 * @brief This file contains the Epic class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Reputation;


use PitPress\Model\Badge\Silver;


/**
 * @brief Earned 200 daily reputation 50 times.
 * @details Awarded once.
 */
class Epic extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Epico";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai ottenuto 200 punti di reputazione per 50 volte. Assegnato una sola volta.
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