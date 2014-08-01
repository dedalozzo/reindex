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
    $this->setPrefix('/utenti/([\da-z_]{5,20})');
    $this->addGet('', ['action' => 'timeline', 'username' => 1]);
    $this->addGet('/timeline/{type}', ['action' => 'timeline', 'username' => 1]);
    $this->addGet('/connessioni/{type}', ['action' => 'connections', 'username' => 1]);
    $this->addGet('/preferiti/{type}', ['action' => 'favorites', 'username' => 1]);
    $this->addGet('/reputatione/{type}', ['action' => 'reputation', 'username' => 1]);
    $this->addGet('/attivita/{type}', ['action' => 'activities', 'username' => 1]);
    $this->addGet('/ricompense/{type}', ['action' => 'bounties', 'username' => 1]);
    $this->addGet('/progetti/{type}', ['action' => 'projects', 'username' => 1]);
  }

}