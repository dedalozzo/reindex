<?php

/**
 * @file AuthGroup.php
 * @brief This file contains the AuthGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Route;


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
        'namespace' => 'PitPress\Controller',
        'controller' => 'auth'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    $this->addGet('/registrati/', ['action' => 'signUp']);
    $this->addGet('/resetta-password/', ['action' => 'resetPassword']);
    $this->addGet('/invia-email-attivazione/', ['action' => 'sendActivationEmail']);
    $this->addGet('/attiva/', ['action' => 'activate']);
    $this->addGet('/disconnetti/', ['action' => 'signOut']);

    // All the following routes start with /accedi.
    $this->setPrefix('/accedi');
    $this->add('/', ['action' => 'signIn'], ['GET', 'POST']);
    $this->addGet('/facebook/', ['action' => 'facebook']);
    $this->addGet('/google/', ['action' => 'google']);
    $this->addGet('/linkedin/', ['action' => 'linkedin']);
    $this->addGet('/github/', ['action' => 'github']);
  }

}