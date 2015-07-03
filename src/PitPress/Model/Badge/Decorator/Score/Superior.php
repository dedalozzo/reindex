<?php

/**
 * @file Superior.php
 * @brief This file contains the Superior class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Score;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Wrote a post with 25 score.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Superior extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Superiore";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai fornito un contributo (articolo, domanda, recensione, link) che ha ottenuto 30 punti. Assegnato più volte.
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
    return ['vote'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}