<?php

/**
 * @file Beacon.php
 * @brief This file contains the Beacon class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Link;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Reported 500 links.
 * @details Awarded once.
 */
class Beacon extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Faro";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai segnalato almeno 500 links. Assegnato una sola volta.
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
    return ['link'];
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