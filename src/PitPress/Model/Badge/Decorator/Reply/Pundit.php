<?php

/**
 * @file Pundit.php
 * @brief This file contains the Pundit class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Reply;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Left 10 comments with score of 5 or more.
 * @details Awarded once.
 * @nosubgrouping
 */
class Pundit extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Dotto";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai lasciato 10 commenti con almeno 5 punti ciascuno. Assegnato una sola volta.
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
  public function update($msg, $data) {

  }

}