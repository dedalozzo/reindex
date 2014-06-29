<?php

/**
 * @file Contributor.php
 * @brief This file contains the Contributor class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Post;


use PitPress\Model\Badge\Silver;


/**
 * @brief Wrote your first article, even on your own blog.
 * @details Awarded once.
 */
class Contributor extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Collaboratore";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai scritto il tuo primo articolo sulla tua timeline. Assegnato una sola volta.
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