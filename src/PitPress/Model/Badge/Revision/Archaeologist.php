<?php

/**
 * @file Archaeologist.php
 * @brief This file contains the Archaeologist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Revision;


use PitPress\Model\Badge\Silver;


/**
 * @brief Edited 100 posts that were inactive for 6 months.
 * @details Awarded once.
 */
class Archaeologist extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Archeologo";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai modificato 100 contributi (articoli, domande, ecc.) che sono stati inattivi per almeno 6 mesi. Assegnato una sola
volta.
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