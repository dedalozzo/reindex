<?php

/**
 * @file Guru.php
 * @brief This file contains the Guru class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Accepted answer and score of 40 or more.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Guru extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Guru";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
La tua risposta è stata accettata dall'autore e ha totalizzato 40 punti. Assegnato più volte.
DESC;
  }


  /**
   * @copydoc Decorator::getMetal()
   */
  public function getMetal() {
    return Metal::SILVER;
  }


  /**
   * @copydoc IObserver::getMessages()
   */
  public function getMessages() {
    return ['vote, accept'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}