<?php

/**
 * @file AuthGroup.php
 * @brief This file contains the AuthGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of Authentication routes.
 * @nosubgrouping
 */
class AuthGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'ReIndex\Controller',
        'controller' => 'auth'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    $this->addGet('/resetpasswd/', ['action' => 'resetPassword']);
    $this->addGet('/sendactemail/', ['action' => 'sendActivationEmail']);
    $this->addGet('/activate/', ['action' => 'activate']);
    $this->addGet('/signout/', ['action' => 'signOut']);

    // All the following routes start with /access.
    $this->setPrefix('/signin');
    $this->add('/', ['action' => 'logon'], ['GET', 'POST']);
    $this->addGet('/facebook/', ['action' => 'facebook']);
    $this->addGet('/google/', ['action' => 'google']);
    $this->addGet('/linkedin/', ['action' => 'linkedin']);
    $this->addGet('/github/', ['action' => 'github']);
  }

}