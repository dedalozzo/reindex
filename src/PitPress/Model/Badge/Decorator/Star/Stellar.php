<?php

/**
 * @file Stellar.php
 * @brief This file contains the Stellar class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Star;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Wrote a post starred by 50 users.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Stellar extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Stellare";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai fornito un contributo (articolo, domanda, recensione, link) aggiunto ai preferiti 50 volte. Assegnato più volte.
DESC;
  }


  /**
   * @copydoc Decorator::getMetal()
   */
  public function getMetal() {
    return Metal::GOLD;
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
  public function update() {

  }

} 