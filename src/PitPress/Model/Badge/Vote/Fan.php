<?php

/**
 * @file Fan.php
 * @brief This file contains the Fan class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Vote;


use PitPress\Enum\Metal;


/**
 * @brief Voted 250 or more times.
 * @details Awarded once.
 */
class Fan extends Attendee {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Fan";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai votato 250 o più volte. Assegnato una sola volta.
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
    return ['vote'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

} 