<?php

/**
 * @file Tagger.php
 * @brief This file contains the Tagger class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Tag;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief First tag description edit.
 * @details Awarded once.
 */
class Tagger extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Graffitaro";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Per la prima volta hai modificato la descrizione di un tag. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return Metal::BRONZE;
  }


  /**
   * @copydoc Badge::getMessages()
   */
  public function getMessages() {
    return ['edit tag'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}