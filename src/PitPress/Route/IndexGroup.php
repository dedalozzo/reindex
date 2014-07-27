<?php

/**
 * @file IndexGroup.php
 * @brief Group of Updates routes.
 * @details
 * @author Filippo F. Fadda
 */


//! PitPress routes namespace.
namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of index routes.
 * @nosubgrouping
 */
class IndexGroup extends Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'index'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    $this->addGet('/', ['action' => 'index']);

    // perDate
    $this->addGet('/([0-9]{4})/', ['action' => 'perDate', 'year' => 1]);
    $this->addGet('/([0-9]{4})/([0-9]{2})/', ['action' => 'perDate', 'year' => 1, 'month' => 2]);
    $this->addGet('/([0-9]{4})/([0-9]{2})/([0-9]{2})/', ['action' => 'perDate', 'year' => 1, 'month' => 2, 'day' => 3]);
    //$this->addGet('/([0-9]{4})/(?:([0-9]{2})/(?:([0-9]{2})/){0,1}){0,1}', ['action' => 'perDate', 'year' => 1, 'month' => 2, 'day' => 3]);

    // All the following routes start with /something.
    $this->setPrefix('/([a-z]{0,20})');
    $this->addGet('/', ['action' => 'newest', 'filter' => 1]);
    $this->addGet('/nuov[ie]/', ['action' => 'newest', 'filter' => 1]);
    $this->addGet('/popolari/', ['action' => 'popular', 'filter' => 1]);
    $this->addGet('/popolari/{period}/', ['action' => 'popular', 'filter' => 1]);
    $this->addGet('/attiv[ie]/', ['action' => 'active', 'filter' => 1]);
    $this->addGet('/interessanti/', ['action' => 'interesting', 'filter' => 1]);

    // perDateByType
    $this->addGet('/([0-9]{4})/', ['action' => 'perDateByType', 'filter' => 1, 'year' => 2]);
    $this->addGet('/([0-9]{4})/([0-9]{2})/', ['action' => 'perDateByType', 'filter' => 1, 'year' => 2, 'month' => 3]);
    $this->addGet('/([0-9]{4})/([0-9]{2})/([0-9]{2})/', ['action' => 'perDateByType', 'filter' => 1, 'year' => 2, 'month' => 3, 'day' => 4]);

    // All the following routes start with /domande.
    $this->setPrefix('/domande');
    $this->addGet('/importanti/', ['action' => 'important']);
    $this->addGet('/aperte/', ['action' => 'open']);
    $this->addGet('/aperte/{filter}/', ['action' => 'open']);

    //$this->addGet('/rss', ['action' => 'rss']);
  }

}