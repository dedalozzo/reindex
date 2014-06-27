<?php

/**
 * @file Columnlist.php
 * @brief This file contains the Columnlist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Post;


use PitPress\Model\Badge\Silver;


/**
 * @brief Wrote at least 10 articles on the same tag.
 * @details Awarded multiple times.
 */
class Columnlist extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Articolista";
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