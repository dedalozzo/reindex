<?php

/**
 * @file BadgeGroup.php
 * @brief This file contains the BadgeGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of Badge routes.
 * @nosubgrouping
 */
class BadgeGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'badge'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    // All the following routes start with /badge.
    $this->setPrefix('/badges');
    $this->addGet('/', ['action' => 'all']);
    $this->addGet('/tutti/', ['action' => 'all']);
    $this->addGet('/ottenuti/', ['action' => 'achieve']);
    $this->addGet('/non-ottenuti/', ['action' => 'notAchieve']);
    $this->addGet('/oro/', ['action' => 'gold']);
    $this->addGet('/argento/', ['action' => 'silver']);
    $this->addGet('/bronzo/', ['action' => 'bronze']);
    $this->addGet('/per-tag/', ['action' => 'byTag']);
    $this->addGet('/per-tag/{filter}/', ['action' => 'byTag']);
  }
}