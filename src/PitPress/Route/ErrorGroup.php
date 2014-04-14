<?php

//! @file ErrorGroup.php
//! @brief This file contains the ErrorGroup class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


//! @brief Group of error routes.
//! @nosubgrouping
class ErrorGroup extends Group {

  public function initialize() {

    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'error'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    $this->addGet('/404/', ['action' => 'show404']);
  }

} 