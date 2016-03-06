<?php

/**
 * @file ApiGroup.php
 * @brief This file contains the ApiGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\Di;


/**
 * @brief Group of API routes.
 * @nosubgrouping
 */
class ApiGroup extends Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'ReIndex\Controller',
        'controller' => 'api'
      ]);

    $this->setHostname('api.'.Di::getDefault()['config']['application']['domainName']);

    $this->addPost('/like/', ['action' => 'like']);
    $this->addPost('/star/', ['action' => 'star']);
    $this->addPost('/submit/', ['action' => 'submit']);
    $this->addPost('/approve/', ['action' => 'approve']);
    $this->addPost('/return-for-revision/', ['action' => 'returnForRevision']);
    $this->addPost('/reject/', ['action' => 'reject']);
    $this->addPost('/revert/', ['action' => 'revert']);
    $this->addPost('/move-to-trash/', ['action' => 'moveToTrash']);
    $this->addPost('/restore/', ['action' => 'restore']);
    $this->addPost('/mark-as-draft/', ['action' => 'markAsDraft']);
    $this->addPost('/close/', ['action' => 'close']);
    $this->addPost('/lock/', ['action' => 'lock']);
    $this->addPost('/unprotect/', ['action' => 'unprotect']);
    $this->addPost('/hide/', ['action' => 'hide']);
    $this->addPost('/show/', ['action' => 'show']);
  }

}