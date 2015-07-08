<?php

/**
 * @file Proofreader.php
 * @brief This file contains the Proofreader class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Revision;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Approved or rejected 100 suggested edits.
 * @details Awarded once.
 * @nosubgrouping
 */
class Proofreader extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Correttore";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai approvato o respinto 100 revisioni. Assegnato una sola volta.
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
    return ['approve, reject'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}