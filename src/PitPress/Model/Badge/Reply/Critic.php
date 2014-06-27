<?php

/**
 * @file Critic.php
 * @brief This file contains the Critic class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Reply;


use PitPress\Model\Badge\Bronze;


/**
 * @brief First to leave a comment.
 * @details Awarded once.
 */
class Critic extends Bronze {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Critico";
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