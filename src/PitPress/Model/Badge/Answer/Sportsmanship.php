<?php

/**
 * @file Sportsmanship.php
 * @brief This file contains the Sportsmanship class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Voted 100 answers on questions where an answer of yours has a positive score.
 * @details Awarded once.
 */
class Sportsmanship extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Sportivo";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai votato 100 risposte su domande dove una delle tue risposte ha almeno un punto. Assegnato una sola volta.
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