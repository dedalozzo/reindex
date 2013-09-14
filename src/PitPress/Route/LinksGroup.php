<?php

//! @file LinksGroup.php
//! @brief Group of Links routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;


//! @brief Group of links' routes.
//! @nosubgrouping
class LinksGroup extends Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'links'
      ]);

    $this->setHostName('links.programmazione.me');

    $this->addGet('/', ['action' => 'popular']);
    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/popolari/{period}', ['action' => 'popular']);
    $this->addGet('/aggiornati/', ['action' => 'updated']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);

    $this->addGet('/rss', ['action' => 'rss']);
  }

}