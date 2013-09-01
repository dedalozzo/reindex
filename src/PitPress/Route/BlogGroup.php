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

    $this->addGet('/nuovi', ['action' => 'newest']);

    $this->addGet('/popolari', ['action' => 'weeklyPopular']);
    $this->addGet('/popolari/settimana', ['action' => 'weeklyPopular']);
    $this->addGet('/popolari/mese', ['action' => 'monthlyPopular']);
    $this->addGet('/popolari/trimestre', ['action' => 'quarterlyPopular']);
    $this->addGet('/popolari/anno', ['action' => 'yearlyPopular']);

    $this->addGet('/aggiornati', ['action' => 'updated']);
    $this->addGet('/interessanti', ['action' => 'interesting']);

    $this->addGet('/articoli', ['action' => 'articles']);
    $this->addGet('/libri', ['action' => 'books']);
    $this->addGet('/tutorial', ['action' => 'tutorials']);

    $this->addGet('/rss', ['action' => 'rss']);
  }

}