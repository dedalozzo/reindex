<?php

/**
 * @file GitHubber.php
 * @brief This file contains the GitHubber class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\User;


use PitPress\Model\Badge\Decorator\Decorator;
use PitPress\Enum\Metal;


/**
 * @brief The user has added this GitHub account.
 * @details Awarded multiple times.
 * @nosubgrouping
 */
class GitHubber extends Decorator {


  /**
   * @copydoc Decorator::getName()
   */
  public function getName() {
    return "GitHubber";
  }


  /**
   * @copydoc Decorator::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai linkato il tuo profilo su GitHub in modo che possano essere visualizzati i tuoi progetti. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Decorator::getMetal()
   */
  public function getMetal() {
    return Metal::SILVER;
  }


  /**
   * @copydoc IObserver::getMessages()
   */
  public function getMessages() {
    return ['github'];
  }


  /**
   * @copydoc IObserver::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

} 