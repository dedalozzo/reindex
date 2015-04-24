<?php

/**
 * @file Tagger.php
 * @brief This file contains the Tagger class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Tag;


use PitPress\Model\Badge\Bronze;


/**
 * @brief First tag description edit.
 * @details Awarded once.
 */
class Tagger extends Bronze {


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
   * @copydoc Badge::exist()
   */
  public function exist() {

  }


  /**
   * @copydoc Badge::deserve()
   */
  public function deserve() {

  }


  /**
   * @copydoc Badge::award()
   */
  public function award() {

  }


  /**
   * @copydoc Badge::withdrawn()
   */
  public function withdrawn() {

  }

}