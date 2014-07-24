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

    $this->addGet('/tour/', ['action' => 'tour']);
    $this->addGet('/aiuto/', ['action' => 'help']);
    $this->addGet('/legale/', ['action' => 'legal']);
    $this->addGet('/privacy/', ['action' => 'privacy']);
    $this->addGet('/lavoro/', ['action' => 'career']);
    $this->addGet('/pubblicita/', ['action' => 'advertising']);
    $this->addGet('/contatti/', ['action' => 'contact']);

    // All the following routes start with /aggiornamenti.
    $this->setPrefix('/aggiornamenti');

    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/popolari/{period}', ['action' => 'popular']);
    $this->addGet('/attivi/', ['action' => 'updated']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);

    //$this->addGet('/rss', ['action' => 'rss']);
  }

}