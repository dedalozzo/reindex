<?php

/**
 * @file Flag/Guardian.php
 * @brief This file contains the Guardian class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Decorator\Flag;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Raised 500 helpful flags.
 * @details Awarded once.
 * @nosubgrouping
 */
class Guardian extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Guardiano";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai segnalato almeno 500 problemi alla redazione. Assegnato una sola volta.
DESC;
  }


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
    return ['flag'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}