<?php

/**
 * @file Flag/Guardian.php
 * @brief This file contains the Guardian class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Flag;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Raised 500 helpful flags.
 * @details Awarded once.
 */
class Guardian extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Guardiano";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai segnalato almeno 500 problemi alla redazione. Assegnato una sola volta.
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
    return ['flag'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}