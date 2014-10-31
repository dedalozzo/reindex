<?php

/**
 * @file TagGroup.php
 * @brief This file contains the TagGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of Tag routes.
 * @nosubgrouping
 */
class TagGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'tag'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    // All the following routes start with /tag.
    $this->setPrefix('/tags');
    $this->addGet('/', ['action' => 'newest']);
    $this->addGet('/popolari/', ['action' => 'popular']);
    $this->addGet('/attivi/', ['action' => 'active']);
    $this->addGet('/per-nome/', ['action' => 'byName']);
    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/sinonimi/', ['action' => 'synonyms']);

    // AJAX calls.
    $this->addPost('/filtra/', ['action' => 'filter']);
  }

}