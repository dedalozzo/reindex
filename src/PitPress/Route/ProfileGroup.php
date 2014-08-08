<?php

/**
 * @file ProfileGroup.php
 * @brief This file contains the ProfileGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of User's Profile routes.
 * @nosubgrouping
 */
class ProfileGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'profile'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    // All the following routes start with /utenti.
    $this->setPrefix('/([\da-z_]{5,20})');

    $this->addGet('', ['action' => 'index', 'username' => 1]);
    $this->addGet('/timeline/', ['action' => 'index', 'username' => 1]);

    $this->addGet('/connessioni/', ['action' => 'connections', 'username' => 1]);
    $this->addGet('/connessioni/{filter}/', ['action' => 'connections', 'username' => 1]);

    $this->addGet('/reputatione/', ['action' => 'reputation', 'username' => 1]);
    $this->addGet('/reputatione/{filter}/', ['action' => 'reputation', 'username' => 1]);

    $this->addGet('/attivita/', ['action' => 'activities', 'username' => 1]);
    $this->addGet('/attivita/{filter}/', ['action' => 'activities', 'username' => 1]);

    $this->addGet('/ricompense/', ['action' => 'bounties', 'username' => 1]);
    $this->addGet('/ricompense/{filter}/', ['action' => 'bounties', 'username' => 1]);

    $this->addGet('/progetti/', ['action' => 'projects', 'username' => 1]);
    $this->addGet('/progetti/{filter}/', ['action' => 'projects', 'username' => 1]);
  }

}