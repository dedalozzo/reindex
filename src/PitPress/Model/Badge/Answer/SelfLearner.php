<?php

/**
 * @file SelfLearner.php
 * @brief This file contains the SelfLearner class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Answered your own question with score of 3 or more.
 * @details Awarded once.
 */
class SelfLearner extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Autodidatta";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai risposto ad una tua stessa domanda e la tua risposta ha totalizzato almeno 3 punti. Assegnato una sola volta.
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
    return ['score'];
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