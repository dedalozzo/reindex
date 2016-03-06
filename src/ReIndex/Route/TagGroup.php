<?php

/**
 * @file TagGroup.php
 * @brief This file contains the TagGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\Di;


/**
 * @brief Group of Tag routes.
 * @nosubgrouping
 */
class TagGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'ReIndex\Controller',
        'controller' => 'tag'
      ]);

    $this->setHostname(Di::getDefault()['config']['application']['domainName']);

    // All the following routes start with /tag.
    $this->setPrefix('/tags');
    $this->addGet('/', ['action' => 'newest']);
    $this->addGet('/popular/', ['action' => 'popular']);
    $this->addGet('/active/', ['action' => 'active']);
    $this->addGet('/name/', ['action' => 'byName']);
    $this->addGet('/new/', ['action' => 'newest']);
    $this->addGet('/synonyms/', ['action' => 'synonyms']);

    // AJAX calls.
    $this->addPost('/filter/', ['action' => 'filter']);
  }

}