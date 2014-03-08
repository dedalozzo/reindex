<?php

//! @file BlogGroup.php
//! @brief Group of Blog routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;


//! @brief Group of blog routes.
//! @nosubgrouping
class BlogGroup extends Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'blog'
      ]);

    $this->setHostName('blog.programmazione.me');

    $this->addGet('/', ['action' => 'newest']);

    $this->addGet('/{year}/{month}/{day}/{slug}', ['action' => 'show']);

    $this->addGet('/{id}/modifica/', ['action' => 'edit']);

    $this->addGet('/articoli/{period}', ['action' => 'articles']);
    $this->addGet('/guide/{period}', ['action' => 'tutorials']);
    $this->addGet('/libri/{period}', ['action' => 'books']);

    // All the following routes start with /pubblicazioni.
    $this->setPrefix('/pubblicazioni');

    $this->addGet('/nuove/', ['action' => 'newest']);
    $this->addGet('/popolari/{period}', ['action' => 'popular']);
    $this->addGet('/aggiornate/', ['action' => 'updated']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);

    //$this->addGet('/rss', ['action' => 'rss']);
  }

}