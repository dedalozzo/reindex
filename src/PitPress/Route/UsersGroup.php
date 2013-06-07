<?php

//! @file UsersGroup.php
//! @brief Group of Users routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


class UsersGroup extends \Phalcon\Mvc\Router\Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'users'
      ]);

    // All the routes start with /utenti.
    $this->setPrefix('/utenti');

    $this->addGet('/', ['action' => 'reputation']);
    $this->addGet('/reputazione', ['action' => 'reputation']);
    $this->addGet('/nuovi', ['action' => 'recents']);
    $this->addGet('/votanti', ['action' => 'votants']);
    $this->addGet('/segnalatori', ['action' => 'contributors']);
    $this->addGet('/autori', ['action' => 'authors']);
    $this->addGet('/editori', ['action' => 'editors']);
    $this->addGet('/moderatori', ['action' => 'moderators']);
  }
}