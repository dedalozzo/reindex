<?php

/**
 * @file AjaxGroup.php
 * @brief This file contains the AjaxGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of AJAX routes.
 * @nosubgrouping
 */
class AjaxGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'ajax'
      ]);

    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    $this->setPrefix('/ajax');

    $this->addPost('/moderator-menu/', ['action' => 'moderatorMenu']);
  }

}