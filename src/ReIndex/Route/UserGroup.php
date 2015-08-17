<?php

/**
 * @file UserGroup.php
 * @brief This file contains the UserGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of User routes.
 * @nosubgrouping
 */
class UserGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'ReIndex\Controller',
        'controller' => 'user'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    // All the following routes start with /utenti.
    $this->setPrefix('/users');
    $this->addGet('/', ['action' => 'newest']);
    $this->addGet('/reputation/', ['action' => 'reputation']);
    $this->addGet('/reputation/{filter}/', ['action' => 'reputation']);
    $this->addGet('/new/', ['action' => 'newest']);
    $this->addGet('/name/', ['action' => 'byName']);
    $this->addGet('/voters/', ['action' => 'voters']);
    $this->addGet('/voters/{filter}/', ['action' => 'voters']);
    $this->addGet('/moderators/', ['action' => 'moderators']);
    $this->addGet('/privileges/', ['action' => 'privileges']);
  }

}