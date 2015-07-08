<?php

/**
 * @file Historian.php
 * @brief This file contains the Historian class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Decorator\Revision;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Edited first post that was inactive for 6 months.
 * @details Awarded once.
 * @nosubgrouping
 */
class Historian extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Storico";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai modificato un contributo che è stato inattivo per 6 mesi. Assegnato una sola volta.
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