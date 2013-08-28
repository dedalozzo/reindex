<?php

//! @file IndexGroup.php
//! @brief Group of Updates routes.
//! @details
//! @author Filippo F. Fadda


//! @brief PitPress routes namespace.
namespace PitPress\Route;


//! @brief
//! @nosubgrouping
class IndexGroup extends \Phalcon\Mvc\Router\Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'index'
      ]);

    $this->addGet('/', ['action' => 'index']);
    $this->addGet('/nuovi', ['action' => 'newest']);

    $this->addGet('/popolari/oggi', ['action' => 'todayPopular']);
    $this->addGet('/popolari/settimana', ['action' => 'weeklyPopular']);
    $this->addGet('/popolari/mese', ['action' => 'monthlyPopular']);
    $this->addGet('/popolari/anno', ['action' => 'yearlyPopular']);
    $this->addGet('/popolari/tutti', ['action' => 'everPopular']);

    $this->addGet('/attivi/oggi', ['action' => 'todayActive']);
    $this->addGet('/attivi/settimana', ['action' => 'weeklyActive']);
    $this->addGet('/attivi/mese', ['action' => 'monthlyActive']);
    $this->addGet('/attivi/anno', ['action' => 'yearlyActive']);
    $this->addGet('/attivi/tutti', ['action' => 'everActive']);

    $this->addGet('/rss', ['action' => 'rss']);
  }

}