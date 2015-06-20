<?php

/**
 * @file Critic.php
 * @brief This file contains the Critic class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Reply;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief First to leave a comment.
 * @details Awarded once.
 */
class Critic extends Badge {


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
    return <<<'DESC'
Hai lasciato il tuo primo commento. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return Metal::BRONZE;
  }


  /**
   * @copydoc Badge::getMessages()
   */
  public function getMessages() {
    return ['comment'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

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