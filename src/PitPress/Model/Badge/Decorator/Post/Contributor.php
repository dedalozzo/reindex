<?php

/**
 * @file Contributor.php
 * @brief This file contains the Contributor class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Post;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Wrote your first article, even on your own blog.
 * @details Awarded once.
 * @nosubgrouping
 */
class Contributor extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Collaboratore";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai scritto il tuo primo articolo sulla tua timeline. Assegnato una sola volta.
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
    return ['article'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}