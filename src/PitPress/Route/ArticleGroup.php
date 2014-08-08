<?php

//! @file ArticleGroup.php
//! @brief This file contains the ArticleGroup class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of Article routes.
 * @nosubgrouping
 */
class ArticleGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'article'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    // All the following routes start with /articoli.
    $this->setPrefix('/articoli');
    $this->addGet('/', ['action' => 'newest']);
    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/popolari/', ['action' => 'popular']);
    $this->addGet('/popolari/{filter}/', ['action' => 'popular']);
    $this->addGet('/attivi/', ['action' => 'active']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);
    $this->addGet('/preferiti/', ['action' => 'favorite']);

    $this->addGet('/([0-9]{4})/', ['action' => 'perDate', 'year' => 1]);
    $this->addGet('/([0-9]{4})/([0-9]{2})/', ['action' => 'perDate', 'year' => 1, 'month' => 2]);
    $this->addGet('/([0-9]{4})/([0-9]{2})/([0-9]{2})/', ['action' => 'perDate', 'year' => 1, 'month' => 2, 'day' => 3]);

    //$this->addGet('/rss', ['action' => 'rss']);
  }

} 