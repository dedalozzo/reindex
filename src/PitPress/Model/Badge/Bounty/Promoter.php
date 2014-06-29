<?php

/**
 * @file Promoter.php
 * @brief This file contains the Promoter class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Bounty;


use PitPress\Model\Badge\Bronze;


/**
 * @brief First bounty offered on your own question.
 * @details Awarded once.
 */
class Promoter extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Promotore";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai offerto il tuo primo bounty su di una tua stessa domanda. Assegnato una sola volta.
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