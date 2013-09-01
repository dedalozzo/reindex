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

    // All the routes start with /tags.
    $this->setPrefix('/tags');

    $this->addGet('/tags', ['action' => 'populars']);
    $this->addGet('/popolari', ['action' => 'populars']);
    $this->addGet('/per-nome', ['action' => 'byName']);
    $this->addGet('/recenti', ['action' => 'recents']);
  }

}