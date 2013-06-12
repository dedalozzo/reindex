<?php

//! @file LinksGroup.php
//! @brief Group of Links routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


//! @brief
//! @nosubgrouping
class LinksGroup extends \Phalcon\Mvc\Router\Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'links'
      ]);

    // All the routes start with /links.
    $this->setPrefix('/links');

    $this->addGet('/links', ['action' => 'recents']);
    $this->addGet('/popolari', ['action' => 'populars']);
    $this->addGet('/recenti', ['action' => 'recents']);
    $this->addGet('/in-base-ai-miei-tag', ['action' => 'basedOnMyTags']);
    $this->addGet('/piu-votati', ['action' => 'mostVoted']);
    $this->addGet('/piu-discussi', ['action' => 'mostDiscussed']);
    $this->addGet('/segnalati-da-me', ['action' => 'postedByMe']);
    $this->addGet('/rss', ['action' => 'rss']);
  }
}