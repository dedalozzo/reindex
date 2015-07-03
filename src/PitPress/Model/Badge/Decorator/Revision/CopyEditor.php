<?php

/**
 * @file CopyEditor.php
 * @brief This file contains the CopyEditor class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Revision;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief Edited 25 posts.
 * @details Awarded once.
 * @nosubgrouping
 */
class CopyEditor extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Copy-editor";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai modificato 25 contributi. Assegnato una sola volta.
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
    return ['edit'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}