<?php

/**
 * @file ProfileGroup.php
 * @brief User's profile routes.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of user's profile routes.
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

    $this->setHostName('utenti.'.DI::getDefault()['config']['application']['domainName']);

    $this->addGet('/{username:[\da-z_]{5,20}}', ['action' => 'timeline']);
    $this->addGet('/{username:[\da-z_]{5,20}}/timeline/{type}', ['action' => 'timeline']);
    $this->addGet('/{username:[\da-z_]{5,20}}/connessioni/{type}', ['action' => 'connections']);
    $this->addGet('/{username:[\da-z_]{5,20}}/preferiti/{type}', ['action' => 'favorites']);
    $this->addGet('/{username:[\da-z_]{5,20}}/reputatione/{type}', ['action' => 'reputation']);
    $this->addGet('/{username:[\da-z_]{5,20}}/attivita/{type}', ['action' => 'activities']);
    $this->addGet('/{username:[\da-z_]{5,20}}/ricompense/{type}', ['action' => 'bounties']);
    $this->addGet('/{username:[\da-z_]{5,20}}/progetti/{type}', ['action' => 'projects']);
  }

}