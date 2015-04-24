<?php

/**
 * @file Excellent.php
 * @brief This file contains the Excellent class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Score;


use PitPress\Model\Badge\Silver;


/**
 * @brief Wrote a post with 15 score.
 * @details Awarded multiple times.
 */
class Excellent extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Eccellente";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai fornito un contributo (articolo, domanda, recensione, link) che ha ottenuto 15 punti. Assegnato più volte.
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