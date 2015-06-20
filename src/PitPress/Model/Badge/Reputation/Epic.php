<?php

/**
 * @file Epic.php
 * @brief This file contains the Epic class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Reputation;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Earned 200 daily reputation 50 times.
 * @details Awarded once.
 */
class Epic extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Epico";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai ottenuto 200 punti di reputazione per 50 volte. Assegnato una sola volta.
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
    return ['reputation'];
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