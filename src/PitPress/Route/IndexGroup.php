<?php

//! @file IndexGroup.php
//! @brief Group of Updates routes.
//! @details
//! @author Filippo F. Fadda


//! @brief PitPress routes namespace.
namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;


//! @brief Group of index routes.
//! @nosubgrouping
class IndexGroup extends Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'index'
      ]);

    $this->addGet('/', ['action' => 'index']);
    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/popolari/', ['action' => 'popular']);
    $this->addGet('/attivi/', ['action' => 'active']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);

    $this->addGet('/rss', ['action' => 'rss']);
  }

}