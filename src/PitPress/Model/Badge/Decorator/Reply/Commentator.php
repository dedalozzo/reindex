<?php

/**
 * @file Commentator.php
 * @brief This file contains the Commentator class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Reply;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Wrote 10 comments.
 * @details Awarded once.
 * @nosubgrouping
 */
class Commentator extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Commentatore";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai scritto 10 commenti. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Decorator::getMetal()
   */
  public function getMetal() {
    return Metal::BRONZE;
  }


  /**
   * @copydoc IObserver::getMessages()
   */
  public function getMessages() {
    return ['comment'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}