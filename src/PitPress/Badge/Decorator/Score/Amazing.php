<?php

/**
 * @file Amazing.php
 * @brief This file contains the Amazing class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Score;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Wrote a post with 100 score.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Amazing extends Decorator {


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
    return ['vote'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}