<?php

/**
 * @file Guru.php
 * @brief This file contains the Guru class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Accepted answer and score of 40 or more.
 * @details Awarded multiple times.
 */
class Guru extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Guru";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
La tua risposta è stata accettata dall'autore e ha totalizzato 40 punti. Assegnato più volte.
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
    return ['vote, accept'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}