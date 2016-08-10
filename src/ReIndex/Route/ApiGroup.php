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

    $this->setHostname(Di::getDefault()['config']['application']['domainName']);

    $this->setPrefix('/api');

    $this->addPost('/member/add-friend/', ['action' => 'addFriend']);
    $this->addPost('/member/remove-friend/', ['action' => 'removeFriend']);

    $this->addPost('/post/like/', ['action' => 'likePost']);
    $this->addPost('/post/close/', ['action' => 'closePost']);
    $this->addPost('/post/lock/', ['action' => 'lockPost']);
    $this->addPost('/post/unprotect/', ['action' => 'unprotectPost']);
    $this->addPost('/post/hide/', ['action' => 'hidePost']);
    $this->addPost('/post/show/', ['action' => 'showPost']);
    $this->addPost('/post/move-to-trash/', ['action' => 'movePostToTrash']);
    $this->addPost('/post/restore/', ['action' => 'restorePost']);

    $this->addPost('/tag/star/', ['action' => 'starTag']);

    $this->addPost('/reply/like/', ['action' => 'likeReply']);
  }

}