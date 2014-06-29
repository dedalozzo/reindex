<?php

/**
 * @file Commentator.php
 * @brief This file contains the Commentator class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Reply;


use PitPress\Model\Badge\Bronze;


/**
 * @brief Wrote 10 comments.
 * @details Awarded once.
 */
class Commentator extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Commentatore";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai scritto 10 commenti. Assegnato una sola volta.
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