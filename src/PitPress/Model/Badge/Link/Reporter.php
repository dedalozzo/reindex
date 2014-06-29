<?php

/**
 * @file Reporter.php
 * @brief This file contains the Reporter class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Link;


use PitPress\Model\Badge\Silver;


/**
 * @brief Reported 25 links.
 * @details Awarded once.
 */
class Reporter extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Corrispondente";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai segnalato almeno 25 links. Assegnato una sola volta.
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