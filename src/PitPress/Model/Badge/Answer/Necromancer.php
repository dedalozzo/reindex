<?php

/**
 * @file Necromancer.php
 * @brief This file contains the Necromancer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Answered a question more than 60 days older with score of 5 or more.
 * @details Awarded multiple times.
 */
class Necromancer extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Negromante";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai risposta ad una domanda formulata almeno 2 mesi prima; la tua risposta ha ottenuto almeno 5 punti. Assegnato più
volte.
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