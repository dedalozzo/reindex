<?php

/**
 * @file Blogger.php
 * @brief This file contains the Blogger class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Post;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Wrote at least 2 articles on your own blog.
 * @details Awarded once.
 * @nosubgrouping
 */
class Blogger extends Decorator {


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
    return ['article'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}