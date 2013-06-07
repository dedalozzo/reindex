<?php

//! @file NewsGroup.php
//! @brief Group of News routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


class NewsGroup extends \Phalcon\Mvc\Router\Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'news'
      ]);

    // All the routes start with /news.
    $this->setPrefix('/news');

    $this->addGet('/news', ['action' => 'recents']);
    $this->addGet('/popolari', ['action' => 'populars']);
    $this->addGet('/recenti', ['action' => 'recents']);
    $this->addGet('/in-base-ai-miei-tag', ['action' => 'basedOnMyTags']);
    $this->addGet('/piu-votate', ['action' => 'mostVoted']);
    $this->addGet('/piu-discusse', ['action' => 'mostDiscussed']);
    $this->addGet('/segnalate-da-me', ['action' => 'postedByMe']);
    $this->addGet('/rss', ['action' => 'rss']);
  }
}