<?php

/**
 * @file Voter.php
 * @brief This file contains the Voter class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Vote;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Voted 10 times in a day.
 * @details Awarded once.
 * @nosubgrouping
 */
class Attendee extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Partecipante";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai votato 10 volte in un giorno. Assegnato una sola volta.
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
    return ['vote'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

} 