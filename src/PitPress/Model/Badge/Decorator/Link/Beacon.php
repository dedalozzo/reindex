<?php

/**
 * @file Beacon.php
 * @brief This file contains the Beacon class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Link;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Reported 500 links.
 * @details Awarded once.
 * @nosubgrouping
 */
class Beacon extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Faro";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai segnalato almeno 500 links. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Decorator::getMetal()
   */
  public function getMetal() {
    return Metal::GOLD;
  }


  /**
   * @copydoc IObserver::getMessages()
   */
  public function getMessages() {
    return ['link'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}