<?php

/**
 * @file Organizer.php
 * @brief This file contains the Organizer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Tag;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief First retag.
 * @details Awarded once.
 * @nosubgrouping
 */
class Organizer extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Organizzatore";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Prima volta che modifichi i tag di contributo. Assegnato una sola volta.
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
    return ['retag'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}