<?php

/**
 * @file FooterGroup.php
 * @brief This file contains the FooterGroup class.
 * @details
 * @author Filippo F. Fadda
 */


//! PitPress routes namespace.
namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of Footer routes.
 * @nosubgrouping
 */
class FooterGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'footer'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    $this->addGet('/tour/', ['action' => 'tour']);
    $this->addGet('/aiuto/', ['action' => 'help']);
    $this->addGet('/legale/', ['action' => 'legal']);
    $this->addGet('/privacy/', ['action' => 'privacy']);
    $this->addGet('/lavoro/', ['action' => 'career']);
    $this->addGet('/pubblicita/', ['action' => 'advertising']);
    $this->addGet('/contatti/', ['action' => 'contact']);

    // RSS feed.
    $this->addGet('/rss', ['action' => 'rss']);
  }

}