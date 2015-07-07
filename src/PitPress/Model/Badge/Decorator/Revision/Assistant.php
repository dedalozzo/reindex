<?php

/**
 * @file Assistant.php
 * @brief This file contains the Assistant class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Revision;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief First edit.
 * @details Awarded once.
 * @nosubgrouping
 */
class Assistant extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Assistente";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
E' la prima volta che editi un contributo, sia esso un articolo, una domanda, una risposta, ecc. Assegnato una sola
volta.
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
    return ['edit'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

} 