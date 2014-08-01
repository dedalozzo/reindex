<?php

/**
 * @file UserGroup.php
 * @brief This file contains the UserGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of User routes.
 * @nosubgrouping
 */
class UserGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'user'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    // All the following routes start with /utenti.
    $this->setPrefix('/utenti');
    $this->addGet('/', ['action' => 'newest']);
    $this->addGet('/reputazione/', ['action' => 'reputation']);
    $this->addGet('/reputazione/{filter}/', ['action' => 'reputation']);
    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/per-nome/', ['action' => 'byName']);
    $this->addGet('/votanti/', ['action' => 'voters']);
    $this->addGet('/votanti/{filter}/', ['action' => 'voters']);
    $this->addGet('/moderatori/', ['action' => 'moderators']);
    $this->addGet('/privilegi/', ['action' => 'privileges']);
  }

}