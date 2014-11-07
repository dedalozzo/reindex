<?php

/**
 * @file PostGroup.php
 * @brief This file contains the PostGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of blog routes.
 * @nosubgrouping
 */
class PostGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'post'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    // Shows post.
    $this->addGet('/{year:[0-9]{4}}/{month:[0-9]{2}}/{day:[0-9]{2}}/{slug:[\da-z-]+}', ['action' => 'show']);

    // Edits post.
    $this->add('/{id}/modifica/', ['action' => 'edit'])->via(['GET', 'POST']);
  }

}