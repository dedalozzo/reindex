<?php

/**
 * @file LinksGroup.php
 * @brief Group of Links routes.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of links' routes.
 * @nosubgrouping
 */
class LinksGroup extends Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'links'
      ]);

    $this->setHostName('links.'.DI::getDefault()['config']['application']['domainName']);

    $this->addGet('/{year:[0-9]{4}}/{month:[0-9]{2}}/{day:[0-9]{2}}/', ['action' => 'perDate']);

    $this->addGet('/{year:[0-9]{4}}/{month:[0-9]{2}}/{day:[0-9]{2}}/{slug:[\da-z-]+}', ['action' => 'show']);

    $this->addGet('/', ['action' => 'popular']);
    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/popolari/{period}', ['action' => 'popular']);
    $this->addGet('/aggiornati/', ['action' => 'updated']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);

    $this->addGet('/rss', ['action' => 'rss']);
  }

}