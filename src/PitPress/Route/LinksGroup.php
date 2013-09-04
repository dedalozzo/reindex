<?php

//! @file LinksGroup.php
//! @brief Group of Links routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;


//! @brief Group of links' routes.
//! @nosubgrouping
class LinksGroup extends Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'links'
      ]);

    // All the routes start with /links.
    $this->setPrefix('/links');

    $this->addGet('/', ['action' => 'newest']);
    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/popolari/', ['action' => 'todayPopular']);
        $this->addGet('/popolari/oggi/', ['action' => 'todayPopular']);
        $this->addGet('/popolari/ieri/', ['action' => 'yesterdayPopular']);
        $this->addGet('/popolari/settimana/', ['action' => 'weeklyPopular']);
        $this->addGet('/popolari/mese/', ['action' => 'monthlyPopular']);
        $this->addGet('/popolari/trimestre/', ['action' => 'quarterlyPopular']);
        $this->addGet('/popolari/anno/', ['action' => 'yearlyPopular']);
        $this->addGet('/popolari/sempre/', ['action' => 'everPopular']);
    $this->addGet('/attivi/', ['action' => 'active']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);

    $this->addGet('/rss', ['action' => 'rss']);
  }

}