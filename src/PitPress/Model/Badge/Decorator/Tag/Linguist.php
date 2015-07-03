<?php

/**
 * @file Linguist.php
 * @brief This file contains the Linguist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Tag;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief First approved tag synonym.
 * @details Awarded once.
 * @nosubgrouping
 */
class Linguist extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Linguista";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Per la prima volta un sinonimo da te inserito è stato approvato. Assegnato una sola volta.
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
    return ['approve synonym'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

} 