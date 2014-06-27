<?php

/**
 * @file Assistant.php
 * @brief This file contains the Assistant class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Revision;


use PitPress\Model\Badge\Bronze;


/**
 * @brief First edit.
 * @details Awarded once.
 */
class Assistant extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Assistente";
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