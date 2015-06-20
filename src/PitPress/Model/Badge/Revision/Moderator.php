<?php

/**
 * @file Moderator.php
 * @brief This file contains the Moderator class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Revision;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Served as a moderator for at least 1 year.
 * @details Awarded once.
 */
class Moderator extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Moderatore";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Sei stato moderatore del sito per almeno un anno. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return Metal::GOLD;
  }


  /**
   * @copydoc Badge::getMessages()
   */
  public function getMessages() {
    return ['time'];
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