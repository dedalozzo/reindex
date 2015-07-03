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
 * @brief The user has added this GitHub account.
 * @details Awarded multiple times.
 */
class GitHubber extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "GitHubber";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai linkato il tuo profilo su GitHub in modo che possano essere visualizzati i tuoi progetti. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return Metal::SILVER;
  }


  /**
   * @copydoc Badge::getMessages()
   */
  public function getMessages() {
    return ['github'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

} 