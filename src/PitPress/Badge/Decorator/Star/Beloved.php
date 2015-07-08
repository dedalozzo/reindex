<?php

/**
 * @file Beloved.php
 * @brief This file contains the Beloved class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Star;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Wrote a post starred by 10 users.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Beloved extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Popolare";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai fornito un contributo (articolo, domanda, recensione, link) aggiunto ai preferiti 10 volte. Assegnato più volte.
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
    return ['star'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}