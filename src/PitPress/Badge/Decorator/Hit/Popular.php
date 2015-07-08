<?php

/**
 * @file Distinguishable.php
 * @brief This file contains the Distinguishable class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Hit;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Wrote a post with 20.000 views.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Popular extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Popolare";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai scritto un articolo o formulato una domanda con almeno 20.000 visualizzazioni. Assegnato più volte.
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
    return ['hit'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}