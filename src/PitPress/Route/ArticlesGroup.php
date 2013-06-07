<?php

//! @file ArticlesGroup.php
//! @brief Group of Articles routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


class ArticlesGroup extends \Phalcon\Mvc\Router\Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'articles'
      ]);

    // All the routes start with /articoli.
    $this->setPrefix('/articoli');

    $this->addGet('/articoli', ['action' => 'recents']);
    $this->addGet('/popolari', ['action' => 'populars']);
    $this->addGet('/recenti', ['action' => 'recents']);
    $this->addGet('/in-base-ai-miei-tag', ['action' => 'basedOnMyTags']);
    $this->addGet('/piu-votati', ['action' => 'mostVoted']);
    $this->addGet('/piu-discussi', ['action' => 'mostDiscussed']);
    $this->addGet('/scritti-da-me', ['action' => 'writtenByMe']);
    $this->addGet('/rss', ['action' => 'rss']);
  }

}