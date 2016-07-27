<?php

/**
 * @file MemberGroup.php
 * @brief This file contains the MemberGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\Di;


/**
 * @brief Group of Member routes.
 * @nosubgrouping
 */
class MemberGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'ReIndex\Controller',
        'controller' => 'member'
      ]);

    $this->setHostname(Di::getDefault()['config']['application']['domainName']);

    // All the following routes start with /utenti.
    $this->setPrefix('/users');
    $this->addGet('/', ['action' => 'newest']);
    $this->addGet('/reputation/', ['action' => 'reputation']);
    $this->addGet('/reputation/{filter}/', ['action' => 'reputation']);
    $this->addGet('/popular/', ['action' => 'popular']);
    $this->addGet('/popular/{filter}/', ['action' => 'popular']);
    $this->addGet('/new/', ['action' => 'newest']);
    $this->addGet('/new/{role}/', ['action' => 'newest']);
    $this->addGet('/new/{role}/{period}/', ['action' => 'newest']);
  }

}