<?php

//! @file TagsGroup.php
//! @brief Group of Tags routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


class TagsGroup extends \Phalcon\Mvc\Router\Group {

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