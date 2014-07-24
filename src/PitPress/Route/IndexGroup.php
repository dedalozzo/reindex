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

    $this->setPrefix('/{filter:[a-z]{0,20}}');
    $this->addGet('/', ['action' => 'newest']);
    $this->addGet('/{year:[0-9]{4}}/{month:[0-9]{2}}/{day:[0-9]{2}}/', ['action' => 'perDate']);
    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/popolari/{period}', ['action' => 'popular']);
    $this->addGet('/attivi/', ['action' => 'active']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);

    // All the following routes start with /domande.
    $this->setPrefix('/domande');
    $this->addGet('/', ['action' => 'important']);
    $this->addGet('/nuove/', ['action' => 'newest']);
    $this->addGet('/popolari/{period}', ['action' => 'popular']);
    $this->addGet('/attive/', ['action' => 'active']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);
    $this->addGet('/importanti/', ['action' => 'important']);
    $this->addGet('/aperte/{type}', ['action' => 'open']);

    //$this->addGet('/rss', ['action' => 'rss']);
  }

}