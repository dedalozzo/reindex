<?php

//! @file UsersGroup.php
//! @brief Group of Users routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


//! @brief Group of users' routes.
//! @nosubgrouping
class UsersGroup extends Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'users'
      ]);

    $this->setHostName('utenti.'.DI::getDefault()['config']['application']['domainName']);

    $this->addGet('/{id}', ['action' => 'show']);
    $this->addGet('/reputazione/{period}', ['action' => 'reputation']);
    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/per-nome/', ['action' => 'byName']);
    $this->addGet('/votanti/{period}', ['action' => 'voters']);
    $this->addGet('/editori/{period}', ['action' => 'editors']);
    $this->addGet('/reporters/{period}', ['action' => 'reporters']);
    $this->addGet('/bloggers/{period}', ['action' => 'bloggers']);
    $this->addGet('/moderatori/', ['action' => 'moderators']);
    $this->addGet('/privilegi/', ['action' => 'privileges']);
  }

}