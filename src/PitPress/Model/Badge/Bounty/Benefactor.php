<?php

/**
 * @file Benefactor.php
 * @brief This file contains the Benefactor class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Bounty;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief First bounty manually awarded on your own question.
 * @details Awarded once.
 */
class Benefactor extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Benefattore";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai assegnato manualmente il tuo primo bounty su di una tua stessa domanda. Assegnato una volta.
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
    return ['bounty'];
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