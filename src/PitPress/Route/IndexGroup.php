<?php

//! @file IndexGroup.php
//! @brief Group of Updates routes.
//! @details
//! @author Filippo F. Fadda


//! @brief PitPress routes namespace.
namespace PitPress\Route;


//! @brief
//! @nosubgrouping
class IndexGroup extends \Phalcon\Mvc\Router\Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'index'
      ]);

    $this->addGet('/', ['action' => 'index']);
    $this->addGet('/popolari', ['action' => 'popular']);
    $this->addGet('/recenti', ['action' => 'latest']);
    $this->addGet('/in-base-ai-miei-tag', ['action' => 'basedOnMyTags']);
    $this->addGet('/piu-votati', ['action' => 'mostVoted']);
    $this->addGet('/piu-discussi', ['action' => 'mostDiscussed']);
    $this->addGet('/rss', ['action' => 'rss']);
  }
}