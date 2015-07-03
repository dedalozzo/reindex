<?php

/**
 * @file Tagger.php
 * @brief This file contains the Tagger class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Tag;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief First tag description edit.
 * @details Awarded once.
 * @nosubgrouping
 */
class Tagger extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Graffitaro";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Per la prima volta hai modificato la descrizione di un tag. Assegnato una sola volta.
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
    return ['edit tag'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}