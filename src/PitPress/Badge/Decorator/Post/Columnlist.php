<?php

/**
 * @file Columnlist.php
 * @brief This file contains the Columnlist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\Post;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Wrote at least 10 articles on the same tag.
 * @details Awarded once.
 * @nosubgrouping
 */
class Columnlist extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Articolista";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai scritto almeno 10 articoli associati ad un particolare tag. Assegnato più volte.
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
  public function update($msg, $data) {

  }

}