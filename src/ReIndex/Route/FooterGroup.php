<?php

/**
 * @file FooterGroup.php
 * @brief This file contains the FooterGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\Di;


/**
 * @brief Group of Footer routes.
 * @nosubgrouping
 */
class FooterGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'ReIndex\Controller',
        'controller' => 'footer'
      ]);

    $this->setHostname(Di::getDefault()['config']['application']['domainName']);

    $this->addGet('/tour/', ['action' => 'tour']);
    $this->addGet('/help/', ['action' => 'help']);
    $this->addGet('/legal/', ['action' => 'legal']);
    $this->addGet('/privacy/', ['action' => 'privacy']);
    $this->addGet('/careers/', ['action' => 'career']);
    $this->addGet('/advertising/', ['action' => 'advertising']);
    $this->addGet('/contacts/', ['action' => 'contact']);
    $this->addGet('/info/', ['action' => 'info']);
  }

}