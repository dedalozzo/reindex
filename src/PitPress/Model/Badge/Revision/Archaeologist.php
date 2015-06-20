<?php

/**
 * @file Archaeologist.php
 * @brief This file contains the Archaeologist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Revision;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Edited 100 posts that were inactive for 6 months.
 * @details Awarded once.
 */
class Archaeologist extends Badge {


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
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return Metal::SILVER;
  }


  /**
   * @copydoc Badge::getMessages()
   */
  public function getMessages() {
    return ['edit'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

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