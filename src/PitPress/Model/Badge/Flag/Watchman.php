<?php

/**
 * @file Watchman.php
 * @brief This file contains the Watchman class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Flag;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief First flagged post.
 * @details Awarded once.
 */
class Watchman extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Vigilante";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai segnalato un problema alla redazione. Assegnato una sola volta.
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
    return ['flag'];
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