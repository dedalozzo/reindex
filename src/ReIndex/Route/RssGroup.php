<?php

/**
 * @file RssGroup.php
 * @brief This file contains the RssGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\Di;


/**
 * @brief Group of RSS feed routes.
 * @nosubgrouping
 */
class RssGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'ReIndex\Controller',
        'controller' => 'rss'
      ]);

    $this->setHostname(Di::getDefault()['config']['application']['domainName']);

    $this->addGet('/rss', ['action' => 'rss']);
  }

}