<?php

//! @file BlogGroup.php
//! @brief Group of Blog routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


//! @brief
//! @nosubgrouping
class BlogGroup extends \Phalcon\Mvc\Router\Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'blog'
      ]);

    // All the routes start with /pubblicazioni.
    $this->setPrefix('/pubblicazioni');

    $this->addGet('/', ['action' => 'recents']);
    $this->addGet('/popolari', ['action' => 'populars']);
    $this->addGet('/recenti', ['action' => 'recents']);
    $this->addGet('/in-base-ai-miei-tag', ['action' => 'basedOnMyTags']);
    $this->addGet('/piu-votate', ['action' => 'mostVoted']);
    $this->addGet('/piu-discusse', ['action' => 'mostDiscussed']);

    $this->addGet('/per-tipo', ['action' => 'all']);
    $this->addGet('/per-tipo/tutte', ['action' => 'all']);
    $this->addGet('/per-tipo/articoli', ['action' => 'articles']);
    $this->addGet('/per-tipo/guide', ['action' => 'tutorials']);
    $this->addGet('/per-tipo/libri', ['action' => 'books']);
    $this->addGet('/per-tipo/sondaggi', ['action' => 'polls']);

    $this->addGet('/mie', ['action' => 'writtenByMe']);
    $this->addGet('/rss', ['action' => 'rss']);
  }

}