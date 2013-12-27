<?php

//! @file AuthGroup.php
//! @brief Group of Updates routes.
//! @details
//! @author Filippo F. Fadda


//! @brief PitPress routes namespace.
namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;


//! @brief Group of authentication routes.
//! @nosubgrouping
class AuthGroup extends Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'auth'
      ]);

    $this->setHostName('programmazione.me');

    $this->addGet('/registrati/', ['action' => 'signUp']);
    $this->addGet('/resetta-password/', ['action' => 'resetPassword']);
    $this->addGet('/invia-email-attivazione/', ['action' => 'sendActivationEmail']);
    $this->addGet('/attiva/', ['action' => 'activate']);
    $this->addGet('/disconnetti/', ['action' => 'signOut']);

    // All the following routes start with /login.
    $this->setPrefix('/accedi');

    $this->addGet('/', ['action' => 'signIn']);
    $this->addGet('/facebook/{params}', ['action' => 'facebook']);
    $this->addGet('/google/{params}', ['action' => 'google']);
    $this->addGet('/linkedin/{params}', ['action' => 'linkedin']);
    $this->addGet('/github/{params}', ['action' => 'github']);
  }

}