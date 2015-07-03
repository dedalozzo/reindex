<?php

/**
 * @file Reporter.php
 * @brief This file contains the Reporter class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Link;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Reported 25 links.
 * @details Awarded once.
 * @nosubgrouping
 */
class Reporter extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Corrispondente";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai segnalato almeno 25 links. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Decorator::getMetal()
   */
  public function getMetal() {
    return Metal::BRONZE;
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