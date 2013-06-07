<?php

//! @file BooksGroup.php
//! @brief Group of Books routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


class BooksGroup extends \Phalcon\Mvc\Router\Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'books'
      ]);

    // All the routes start with /libri.
    $this->setPrefix('/libri');

    $this->addGet('/libri', ['action' => 'recents']);
    $this->addGet('/popolari', ['action' => 'populars']);
    $this->addGet('/recenti', ['action' => 'recents']);
    $this->addGet('/in-base-ai-miei-tag', ['action' => 'basedOnMyTags']);
    $this->addGet('/piu-votati', ['action' => 'mostvoted']);
    $this->addGet('/piu-discussi', ['action' => 'mostdiscussed']);
    $this->addGet('/scritte-da-me', ['action' => 'madebyme']);
    $this->addGet('/rss', ['action' => 'rss']);
  }
}