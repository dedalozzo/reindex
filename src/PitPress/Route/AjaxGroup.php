<?php

/**
 * @file AjaxGroup.php
 * @brief Group of AJAX routes.
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

    $this->setHostName('ajax.'.DI::getDefault()['config']['application']['domainName']);

    $this->addPost('/vota/', ['action' => 'vote']);
    $this->addGet('/pippo/{id}', ['action' => 'pippo']);
  }

}