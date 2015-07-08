<?php

/**
 * @file Promoter.php
 * @brief This file contains the Promoter class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Bounty;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief First bounty offered on your own question.
 * @details Awarded once.
 * @nosubgrouping
 */
class Promoter extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Promotore";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai offerto il tuo primo bounty su di una tua stessa domanda. Assegnato una sola volta.
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
    return ['bounty'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}