<?php

/**
 * @file Librarian.php
 * @brief This file contains the Librarian class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Tag;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Edited 20 tag descriptions.
 * @details Awarded once.
 * @nosubgrouping
 */
class Librarian extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Bibliotecario";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai modificato la descrizione di 20 tags. Assegnato una sola volta.
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
    return ['tag'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

} 