<?php

/**
 * @file Autobiographer.php
 * @brief This file contains the Autobiographer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\User;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Completed all user profile fields.
 * @details Awarded once.
 * @nosubgrouping
 */
class Autobiographer extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Autobiografo";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai completato in ogni sua parte il tuo profilo. Assegnato una sola volta.
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
    return ['completed profile'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

} 