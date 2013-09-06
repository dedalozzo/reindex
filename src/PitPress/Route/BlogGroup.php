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

    // All the routes start with /blog.
    $this->setPrefix('/blog');

    $this->addGet('/', ['action' => 'newest']);
    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/popolari/{period}', ['action' => 'popular']);
    $this->addGet('/aggiornati/', ['action' => 'updated']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);
    $this->addGet('/articoli/{period}', ['action' => 'articles']);
    $this->addGet('/guide/{period}', ['action' => 'tutorials']);
    $this->addGet('/libri/{period}', ['action' => 'books']);

    $this->addGet('/rss', ['action' => 'rss']);
  }

}