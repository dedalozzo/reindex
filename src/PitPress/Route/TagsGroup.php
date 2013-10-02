<?php

//! @file TagsGroup.php
//! @brief Group of Tags routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;


//! @brief Group of tags' routes.
//! @nosubgrouping
class TagsGroup extends Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'tags'
      ]);

    $this->setHostName('tags.programmazione.me');

    $this->addGet('/', ['action' => 'newest']);
    $this->addGet('/popolari/', ['action' => 'popular']);
    $this->addGet('/per-nome/', ['action' => 'byName']);
    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/sinonimi/', ['action' => 'synonyms']);
  }

}