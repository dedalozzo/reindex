<?php

/**
 * @file Librarian.php
 * @brief This file contains the Librarian class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Tag;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Edited 20 tag descriptions.
 * @details Awarded once.
 */
class Librarian extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Bibliotecario";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai modificato la descrizione di 20 tags. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return Metal::GOLD;
  }


  /**
   * @copydoc Badge::getMessages()
   */
  public function getMessages() {
    return ['tag'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

} 