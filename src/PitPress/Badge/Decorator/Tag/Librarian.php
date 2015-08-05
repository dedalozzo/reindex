<?php

/**
 * @file Librarian.php
 * @brief This file contains the Librarian class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Tag;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Edited 20 tag descriptions.
 * @details Awarded once.
 * @nosubgrouping
 */
class Librarian extends Decorator {


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
  public function update($msg, $data) {

  }

} 