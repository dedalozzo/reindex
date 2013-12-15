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

    // All the following routes start with /login.
    $this->setPrefix('/login');

    //$this->addGet('/login/{provider}', ['action' => 'login']);
    //$this->addGet('/{provider}', ['action' => 'authenticate']);

    $this->addGet('/', ['action' => 'login']);
    $this->addGet('/facebook/{params}', ['action' => 'facebook']);
    $this->addGet('/google/{params}', ['action' => 'google']);
    $this->addGet('/linkedin/{params}', ['action' => 'linkedin']);
    $this->addGet('/github/{params}', ['action' => 'github']);
  }

}