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
    $di = DI::getDefault();

    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'ReIndex\Controller',
        'controller' => 'profile'
      ]);

    $this->setHostName($di['config']['application']['domainName']);

    $this->setPrefix('/([\da-zA-Z.\-_]{'.$di['config']['application']['usernameMinLength'].','.($di['config']['application']['usernameMaxLength']+10).'})');

    $this->addGet('', ['action' => 'index', 'username' => 1]);
    $this->addGet('/timeline/', ['action' => 'index', 'username' => 1]);

    $this->addGet('/about/', ['action' => 'about', 'username' => 1]);

    $this->addGet('/connections/', ['action' => 'connections', 'username' => 1]);
    $this->addGet('/connections/{filter}/', ['action' => 'connections', 'username' => 1]);

    $this->addGet('/projects/', ['action' => 'projects', 'username' => 1]);
    $this->addGet('/projects/{filter}/', ['action' => 'projects', 'username' => 1]);

    $this->setPrefix('/settings');
    $this->add('/general/', ['action' => 'general'], ['GET', 'POST']);
    $this->add('/emails/', ['action' => 'emails'], ['GET', 'POST']);
    $this->add('/logins/', ['action' => 'logins'], ['GET', 'POST']);
    $this->add('/privacy/', ['action' => 'privacy'], ['GET', 'POST']);
  }

}