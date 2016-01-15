<?php

/**
 * @file ProfileGroup.php
 * @brief This file contains the ProfileGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of Member's Profile routes.
 * @nosubgrouping
 */
class ProfileGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'ReIndex\Controller',
        'controller' => 'profile'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    $this->setPrefix('/([\da-zA-Z.\-_]{5,24})');

    $this->addGet('', ['action' => 'index', 'username' => 1]);
    $this->addGet('/timeline/', ['action' => 'index', 'username' => 1]);

    $this->addGet('/connections/', ['action' => 'connections', 'username' => 1]);
    $this->addGet('/connections/{filter}/', ['action' => 'connections', 'username' => 1]);

    $this->addGet('/reputation/', ['action' => 'reputation', 'username' => 1]);
    $this->addGet('/reputation/{filter}/', ['action' => 'reputation', 'username' => 1]);

    $this->addGet('/activity/', ['action' => 'activities', 'username' => 1]);
    $this->addGet('/activity/{filter}/', ['action' => 'activities', 'username' => 1]);

    $this->addGet('/bounties/', ['action' => 'bounties', 'username' => 1]);
    $this->addGet('/bounties/{filter}/', ['action' => 'bounties', 'username' => 1]);

    $this->addGet('/projects/', ['action' => 'projects', 'username' => 1]);
    $this->addGet('/projects/{filter}/', ['action' => 'projects', 'username' => 1]);
  }

}