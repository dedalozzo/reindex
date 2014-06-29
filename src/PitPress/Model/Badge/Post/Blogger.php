<?php

/**
 * @file Blogger.php
 * @brief This file contains the Blogger class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Post;


use PitPress\Model\Badge\Silver;


/**
 * @brief Wrote at least 2 articles on your own blog.
 * @details Awarded once.
 */
class Blogger extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Blogger";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai scritto almeno 5 articoli sulla tua timeline. Assegnato una sola volta.
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