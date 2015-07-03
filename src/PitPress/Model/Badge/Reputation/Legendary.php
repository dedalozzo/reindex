<?php

/**
 * @file Legendary.php
 * @brief This file contains the Legendary class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Reputation;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Earned 200 daily reputation 150 times.
 * @details Awarded once.
 */
class Legendary extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Leggendario";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai ottenuto 200 punti di reputazione per 150 volte. Assegnato una sola volta.
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
    return ['reputation'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}