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
    $this->addGet('/nuovi', ['action' => 'newest']);

    $this->addGet('/popolari', ['action' => 'todayPopular']);
    $this->addGet('/popolari/oggi', ['action' => 'todayPopular']);
    $this->addGet('/popolari/ieri', ['action' => 'yesterdayPopular']);
    $this->addGet('/popolari/settimana', ['action' => 'weeklyPopular']);
    $this->addGet('/popolari/mese', ['action' => 'monthlyPopular']);
    $this->addGet('/popolari/trimestre', ['action' => 'quarterlyPopular']);
    $this->addGet('/popolari/anno', ['action' => 'yearlyPopular']);
    $this->addGet('/popolari/sempre', ['action' => 'everPopular']);

    $this->addGet('/attivi', ['action' => 'active']);
    $this->addGet('/interessanti', ['action' => 'interesting']);

    $this->addGet('/rss', ['action' => 'rss']);
  }

}