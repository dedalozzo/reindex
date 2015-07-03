<?php

/**
 * @file Enthusiast.php
 * @brief This file contains the Enthusiast class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Visit;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Visited the site each day for 30 consecutive days.
 * @details Awarded once.
 */
class Enthusiast extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Entusiasta";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai visitato il sito per 30 giorni consecutivi. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return Metal::SILVER;
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

}