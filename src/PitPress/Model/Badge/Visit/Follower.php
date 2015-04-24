<?php

/**
 * @file Follower.php
 * @brief This file contains the Follower class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Visit;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Visited the site each day for 7 consecutive days.
 * @details Awarded once.
 */
class Follower extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Seguace";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai visitato il sito per 7 giorni consecutivi. Assegnato una sola volta.
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