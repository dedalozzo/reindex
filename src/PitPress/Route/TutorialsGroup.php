<?php

//! @file TutorialsGroup.php
//! @brief Group of Tutorials routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


class TutorialsGroup extends \Phalcon\Mvc\Router\Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'tutorials'
      ]);

    // All the routes start with /guide.
    $this->setPrefix('/guide');

    $this->addGet('/guide', ['action' => 'recents']);
    $this->addGet('/popolari', ['action' => 'populars']);
    $this->addGet('/recenti', ['action' => 'recents']);
    $this->addGet('/in-base-ai-miei-tag', ['action' => 'basedOnMyTags']);
    $this->addGet('/piu-votate', ['action' => 'mostVoted']);
    $this->addGet('/piu-discusse', ['action' => 'mostDiscussed']);
    $this->addGet('/redatte-da-me', ['action' => 'writtenByMe']);
    $this->addGet('/rss', ['action' => 'rss']);
  }
}