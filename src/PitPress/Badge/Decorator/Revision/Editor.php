<?php

/**
 * @file Editor.php
 * @brief This file contains the Editor class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Revision;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Edited 100 posts.
 * @details Awarded once.
 * @nosubgrouping
 */
class Editor extends Decorator {


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
    return ['edit'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}