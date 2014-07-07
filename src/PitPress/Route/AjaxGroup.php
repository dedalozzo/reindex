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

    $this->addGet('/registrati/', ['action' => 'signUp']);

    // All the following routes start with /accedi.
    $this->setPrefix('/accedi');

    $this->add('/', ['action' => 'signIn'], ['GET', 'POST']);
    $this->addGet('/facebook/', ['action' => 'facebook']);
    $this->addGet('/google/', ['action' => 'google']);
    $this->addGet('/linkedin/', ['action' => 'linkedin']);
    $this->addGet('/github/', ['action' => 'github']);
  }

}