<?php

/**
 * @file Follower.php
 * @brief This file contains the Follower class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Decorator\Visit;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Visited the site each day for 7 consecutive days.
 * @details Awarded once.
 * @nosubgrouping
 */
class Follower extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Seguace";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai visitato il sito per 7 giorni consecutivi. Assegnato una sola volta.
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
    return ['time'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

} 