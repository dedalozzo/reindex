<?php

/**
 * @file BadgesGroup.php
 * @brief Group of Badges routes.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of badges' routes.
 * @nosubgrouping
 */
class BadgesGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'badges'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    // All the following routes start with /badges.
    $this->setPrefix('/badges');
    $this->addGet('/', ['action' => 'all']);
    $this->addGet('/tutti/', ['action' => 'all']);
    $this->addGet('/ottenuti/', ['action' => 'achieve']);
    $this->addGet('/non-ottenuti/', ['action' => 'notAchieve']);
    $this->addGet('/oro/', ['action' => 'gold']);
    $this->addGet('/argento/', ['action' => 'silver']);
    $this->addGet('/bronzo/', ['action' => 'bronze']);
    $this->addGet('/per-tag/{type}', ['action' => 'byTag']);
  }
}