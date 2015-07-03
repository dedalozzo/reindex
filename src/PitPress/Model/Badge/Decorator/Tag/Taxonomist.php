<?php

/**
 * @file Taxonomist.php
 * @brief This file contains the Taxonomist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Tag;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Created a tag used by 50 posts.
 * @details Awarded once.
 * @nosubgrouping
 */
class Taxonomist extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Tassonomista";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai creato un tag utilizzato da almeno 50 contributi. Assegnato una sola volta.
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
    return ['save post'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

} 