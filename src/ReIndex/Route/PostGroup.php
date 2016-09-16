<?php

/**
 * @file PostGroup.php
 * @brief This file contains the PostGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\Di;


/**
 * @brief Group of post routes.
 * @nosubgrouping
 */
class PostGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'ReIndex\Controller',
        'controller' => 'index' // We don't use post controller, but the index controller.
      ]);

    $this->setHostname(Di::getDefault()['config']['application']['domainName']);

    //$this->addGet('/{id}/', ['action' => 'displayById']);
    $this->addGet('/{year:[0-9]{4}}/{month:[0-9]{2}}/{day:[0-9]{2}}/{slug:[\da-z-]+}', ['action' => 'displayBySlug']);
    $this->add('/{id}/edit/', ['action' => 'edit'])->via(['GET', 'POST']);
  }

}