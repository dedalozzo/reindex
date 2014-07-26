<?php

/**
 * @file TagsGroup.php
 * @brief Group of Tags routes.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of tags' routes.
 * @nosubgrouping
 */
class TagsGroup extends Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'tags'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    // All the following routes start with /tags.
    $this->setPrefix('/tags');
    $this->addGet('/', ['action' => 'newest']);
    $this->addGet('/popolari/', ['action' => 'popular']);
    $this->addGet('/per-nome/', ['action' => 'byName']);
    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/sinonimi/', ['action' => 'synonyms']);
  }

}