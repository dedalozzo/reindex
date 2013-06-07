<?php

//! @file BadgesGroup.php
//! @brief Group of Badges routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


class BadgesGroup extends \Phalcon\Mvc\Router\Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'badges'
      ]);

    // All the routes start with /tags.
    $this->setPrefix('/badges');

    $this->addGet('/badges', ['action' => 'all']);
    $this->addGet('/oro', ['action' => 'gold']);
    $this->addGet('/argento', ['action' => 'silver']);
    $this->addGet('/bronzo', ['action' => 'bronze']);

    $this->addGet('/per-tag', ['action' => 'allByTag']);
    $this->addGet('/per-tag/tutti', ['action' => 'allByTag']);
    $this->addGet('/per-tag/oro', ['action' => 'GoldByTag']);
    $this->addGet('/per-tag/argento', ['action' => 'silverByTag']);
    $this->addGet('/per-tag/bronzo', ['action' => 'bronzeByTag']);
  }
}