<?php

/**
 * @file Batsman.php
 * @brief This file contains the Batsman class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Link;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Reported your first link.
 * @details Awarded once.
 */
class Batsman extends Badge {


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
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return Metal::BRONZE;
  }


  /**
   * @copydoc Badge::getMessages()
   */
  public function getMessages() {
    return ['link'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}