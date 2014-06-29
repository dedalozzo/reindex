<?php

/**
 * @file Groupie.php
 * @brief This file contains the Groupie class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Vote;


use PitPress\Model\Badge\Gold;


/**
 * @brief Voted at least 500 times.
 * @details Awarded once.
 */
class Groupie extends Gold {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Groupie";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai votato almeno 500 volte. Assegnato una sola volta.
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