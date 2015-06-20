<?php

/**
 * @file Voter.php
 * @brief This file contains the Voter class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Vote;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Voted 10 times in a day.
 * @details Awarded once.
 */
class Attendee extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Partecipante";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai votato 10 volte in un giorno. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return Metal::BRONZE;
  }


  /**
   * @copydoc Badge::getMessages()
   */
  public function getMessages() {
    return ['vote'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }


  /**
   * @copydoc Badge::exist()
   */
  public function exist() {

  }


  /**
   * @copydoc Badge::deserve()
   */
  public function deserve() {

  }


  /**
   * @copydoc Badge::award()
   */
  public function award() {

  }


  /**
   * @copydoc Badge::withdrawn()
   */
  public function withdrawn() {

  }

} 