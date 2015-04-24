<?php

/**
 * @file Batsman.php
 * @brief This file contains the Batsman class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Link;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Reported your first link.
 * @details Awarded once.
 */
class Batsman extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Segnalatore";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai segnalato il tuo primo link. Assegnato una sola volta.
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