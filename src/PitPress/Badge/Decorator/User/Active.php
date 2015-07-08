<?php

/**
 * @file GitHubber.php
 * @brief This file contains the GitHubber class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Badge\Decorator\User;


use PitPress\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief The user has activated his profile.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class Active extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "Attivo";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Il tuo profilo è attivo. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Decorator::getMetal()
   */
  public function getMetal() {
    return Metal::BRONZE;
  }


  /**
   * @copydoc IObserver::getMessages()
   */
  public function getMessages() {
    return ['activate profile'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update($msg, $data) {

  }

}