<?php

/**
 * @file Doorman.php
 * @brief This file contains the Doorman class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Flag;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Raised 50 helpful flags.
 * @details Awarded once.
 * @nosubgrouping
 */
class Doorman extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Portinaio";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai segnalato almeno 50 problemi alla redazione. Assegnato una sola volta.
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
    return ['flag'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}