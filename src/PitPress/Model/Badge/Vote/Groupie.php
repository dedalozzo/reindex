<?php

/**
 * @file Groupie.php
 * @brief This file contains the Groupie class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Vote;


use PitPress\Enum\Metal;


/**
 * @brief Voted at least 500 times.
 * @details Awarded once.
 */
class Groupie extends Fan {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Groupie";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai votato almeno 500 volte. Assegnato una sola volta.
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
    return ['vote'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

} 