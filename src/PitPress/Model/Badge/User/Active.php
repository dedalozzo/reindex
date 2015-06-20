<?php

/**
 * @file GitHubber.php
 * @brief This file contains the GitHubber class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\User;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief The user has activated his profile.
 * @details Awarded multiple times.
 */
class Active extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Attivo";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Il tuo profilo è attivo. Assegnato una sola volta.
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
    return ['activate profile'];
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