<?php

/**
 * @file Editor.php
 * @brief This file contains the Editor class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Revision;


use PitPress\Model\Badge\Gold;


/**
 * @brief Edited 100 posts.
 * @details Awarded once.
 */
class Editor extends Gold {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Editore";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai modificato 100 contributi. Assegnato una sola volta.
DESC;
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