<?php

/**
 * @file Linguist.php
 * @brief This file contains the Linguist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Tag;


use PitPress\Model\Badge\Bronze;


/**
 * @brief First approved tag synonym.
 * @details Awarded once.
 */
class Linguist extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Linguista";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return "Primo a rispondere ad una domanda.";
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