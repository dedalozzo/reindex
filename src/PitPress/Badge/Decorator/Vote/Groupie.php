<?php

/**
 * @file Groupie.php
 * @brief This file contains the Groupie class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Vote;


use PitPress\Enum\Metal;


/**
 * @brief Voted at least 500 times.
 * @details Awarded once.
 * @nosubgrouping
 */
class Groupie extends Fan {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Groupie";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai votato almeno 500 volte. Assegnato una sola volta.
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
    return ['vote'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

} 