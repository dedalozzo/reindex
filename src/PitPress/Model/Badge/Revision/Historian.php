<?php

/**
 * @file Historian.php
 * @brief This file contains the Historian class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Revision;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Edited first post that was inactive for 6 months.
 * @details Awarded once.
 */
class Historian extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Storico";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai modificato un contributo che è stato inattivo per 6 mesi. Assegnato una sola volta.
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