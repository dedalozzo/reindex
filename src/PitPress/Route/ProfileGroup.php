<?php

//! @file ProfileGroup.php
//! @brief User's profile routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


//! @brief Group of user's profile routes.
//! @nosubgrouping
class ProfileGroup extends Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'profile'
      ]);

    $this->setHostName('utenti.'.DI::getDefault()['config']['application']['domainName']);

    $this->addGet('/{id}', ['action' => 'timeline']);
    $this->addGet('/{id}/blog/{type}', ['action' => 'timeline']);
    $this->addGet('/{id}/connessioni/{type}', ['action' => 'connections']);
    $this->addGet('/{id}/preferiti/{type}', ['action' => 'favourites']);
    $this->addGet('/{id}/reputatione/{type}', ['action' => 'reputation']);
    $this->addGet('/{id}/attivita/{type}', ['action' => 'activities']);
    $this->addGet('/{id}/bounties/{type}', ['action' => 'bounties']);
  }

}