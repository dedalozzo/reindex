<?php

/**
 * @file BookReviewer.php
 * @brief This file contains the BookReviewer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Post;


use PitPress\Model\Badge\Silver;


/**
 * @brief Wrote at least 2 reviews.
 * @details Awarded once.
 */
class BookReviewer extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Recensore";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai scritto almeno 2 recensioni, che sono poi state pubblicate sul blog principale. Assegnato una sola volta.
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