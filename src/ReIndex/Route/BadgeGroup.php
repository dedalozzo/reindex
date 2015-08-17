<?php

/**
 * @file BadgeGroup.php
 * @brief This file contains the BadgeGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Route;


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
        'namespace' => 'ReIndex\Controller',
        'controller' => 'badge'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    // All the following routes start with /badge.
    $this->setPrefix('/badges');
    $this->addGet('/', ['action' => 'all']);
    $this->addGet('/all/', ['action' => 'all']);
    $this->addGet('/earned/', ['action' => 'earned']);
    $this->addGet('/unearned/', ['action' => 'unearned']);
    $this->addGet('/gold/', ['action' => 'gold']);
    $this->addGet('/silver/', ['action' => 'silver']);
    $this->addGet('/bronze/', ['action' => 'bronze']);
    $this->addGet('/tag/', ['action' => 'tag']);
    $this->addGet('/tag/{filter}/', ['action' => 'tag']);
  }
}