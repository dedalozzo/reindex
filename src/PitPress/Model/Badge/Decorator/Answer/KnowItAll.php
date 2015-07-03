<?php

/**
 * @file KnowItAll.php
 * @brief This file contains the KnowItAll class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Provided answers of 15 total score in 20 of top 40 tags.
 * @details Awarded once.
 * @nosubgrouping
 */
class KnowItAll extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Tuttologo";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai fornito risposte con almeno 15 punti, in 20 dei 40 tag più attivi. Assegnato una sola volta.
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
    return ['vote'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}