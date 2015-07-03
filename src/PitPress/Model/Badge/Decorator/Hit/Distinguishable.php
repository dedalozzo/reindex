<?php

/**
 * @file Distinguishable.php
 * @brief This file contains the Distinguishable class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Hit;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Wrote a post with 10.000 views.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Distinguishable extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Riconoscibile";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai scritto un articolo o formulato una domanda con almeno 10.000 visualizzazioni. Assegnato più volte.
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
    return ['hit'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}