<?php

/**
 * @file Archaeologist.php
 * @brief This file contains the Archaeologist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Revision;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Edited 100 posts that were inactive for 6 months.
 * @details Awarded once.
 * @nosubgrouping
 */
class Archaeologist extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Archeologo";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai modificato 100 contributi (articoli, domande, ecc.) che sono stati inattivi per almeno 6 mesi. Assegnato una sola
volta.
DESC;
  }


  /**
   * @copydoc Decorator::getMetal()
   */
  public function getMetal() {
    return Metal::SILVER;
  }


  /**
   * @copydoc IObserver::getMessages()
   */
  public function getMessages() {
    return ['edit'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}