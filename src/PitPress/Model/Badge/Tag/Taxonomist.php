<?php

/**
 * @file Taxonomist.php
 * @brief This file contains the Taxonomist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Tag;


use PitPress\Model\Badge\Silver;


/**
 * @brief Created a tag used by 50 questions.
 * @details Awarded once.
 */
class Taxonomist extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Tassonomista";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai creato un tag utilizzato da almeno 50 contributi. Assegnato una sola volta.
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