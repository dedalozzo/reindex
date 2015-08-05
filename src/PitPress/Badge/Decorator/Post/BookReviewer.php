<?php

/**
 * @file BookReviewer.php
 * @brief This file contains the BookReviewer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Post;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Wrote at least 2 reviews.
 * @details Awarded once.
 * @nosubgrouping
 */
class BookReviewer extends Decorator {


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
    return ['book'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}