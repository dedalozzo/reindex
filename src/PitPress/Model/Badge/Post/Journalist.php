<?php

/**
 * @file Journalist.php
 * @brief This file contains the Journalist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Post;


use PitPress\Model\Badge\Silver;


/**
 * @brief Wrote at least 25 articles.
 * @details Awarded once.
 */
class Journalist extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Giornalista";
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