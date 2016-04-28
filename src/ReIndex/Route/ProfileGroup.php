<?php

/**
 * @file ProfileGroup.php
 * @brief This file contains the ProfileGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\Di;


/**
 * @brief Group of Member's Profile routes.
 * @nosubgrouping
 */
class ProfileGroup extends Group {

  public function initialize() {
    $di = Di::getDefault();

    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'ReIndex\Controller',
        'controller' => 'profile'
      ]);

    $this->setHostname($di['config']['application']['domainName']);

    $this->setPrefix('/([\da-zA-Z.\-_]{'.$di['config']['application']['usernameMinLength'].','.($di['config']['application']['usernameMaxLength']+10).'})');

    $this->addGet('', ['action' => 'index', 'username' => 1]);
    $this->addGet('/timeline/', ['action' => 'index', 'username' => 1]);

    $this->addGet('/about/', ['action' => 'about', 'username' => 1]);

    $this->addGet('/connections/', ['action' => 'connections', 'username' => 1]);
    $this->addGet('/connections/{filter}/', ['action' => 'connections', 'username' => 1]);

    $this->addGet('/repositories/', ['action' => 'repositories', 'username' => 1]);
    $this->addGet('/repositories/{filter}/', ['action' => 'repositories', 'username' => 1]);

    $this->add('/settings/', ['action' => 'info', 'username' => 1], ['GET', 'POST']);
    $this->add('/settings/password/', ['action' => 'password', 'username' => 1], ['GET', 'POST']);
    $this->add('/settings/username/', ['action' => 'username', 'username' => 1], ['GET', 'POST']);
    $this->add('/settings/emails/', ['action' => 'emails', 'username' => 1], ['GET', 'POST']);
    $this->add('/settings/logins/', ['action' => 'logins', 'username' => 1], ['GET', 'POST']);
    $this->add('/settings/privacy/', ['action' => 'privacy', 'username' => 1], ['GET', 'POST']);
    $this->add('/settings/blacklist/', ['action' => 'blacklist', 'username' => 1], ['GET', 'POST']);
  }

}