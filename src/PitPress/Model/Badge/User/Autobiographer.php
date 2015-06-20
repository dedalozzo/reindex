<?php

/**
 * @file Autobiographer.php
 * @brief This file contains the Autobiographer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\User;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Completed all user profile fields.
 * @details Awarded once.
 */
class Autobiographer extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Autobiografo";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai completato in ogni sua parte il tuo profilo. Assegnato una sola volta.
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
    return ['completed profile'];
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