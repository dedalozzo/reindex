<?php

/**
 * @file ApiGroup.php
 * @brief This file contains the ApiGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of API routes.
 * @nosubgrouping
 */
class ApiGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'api'
      ]);

    $this->setHostName('api.'.DI::getDefault()['config']['application']['domainName']);

    //$this->addPost('/like/', ['action' => 'like']);
    $this->addPost('/star/', ['action' => 'star']);
    $this->addPost('/move-to-trash/', ['action' => 'moveToTrash']);
    $this->addPost('/restore/', ['action' => 'restore']);
  }

}