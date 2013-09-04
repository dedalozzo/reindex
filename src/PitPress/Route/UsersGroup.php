<?php

//! @file UsersGroup.php
//! @brief Group of Users routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;


//! @brief Group of users' routes.
//! @nosubgrouping
class UsersGroup extends Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'users'
      ]);

    // All the routes start with /utenti.
    //$this->setPrefix('/utenti');

    $this->addGet('/reputazione/', ['action' => 'weeklyReputation']);
        $this->addGet('/reputazione/settimana/', ['action' => 'weeklyReputation']);
        $this->addGet('/reputazione/mese/', ['action' => 'monthlyReputation']);
        $this->addGet('/reputazione/trimestre/', ['action' => 'quarterlyReputation']);
        $this->addGet('/reputazione/anno/', ['action' => 'yearlyReputation']);
        $this->addGet('/reputazione/sempre/', ['action' => 'everReputation']);
    $this->addGet('/utenti/', ['action' => 'newest']);
    $this->addGet('/votanti/', ['action' => 'weeklyVoters']);
        $this->addGet('/votanti/settimana/', ['action' => 'weeklyVoters']);
        $this->addGet('/votanti/mese/', ['action' => 'monthlyVoters']);
        $this->addGet('/votanti/trimestre/', ['action' => 'quarterlyVoters']);
        $this->addGet('/votanti/anno/', ['action' => 'yearlyVoters']);
        $this->addGet('/votanti/sempre/', ['action' => 'everVoters']);
    $this->addGet('/editori/', ['action' => 'weeklyEditors']);
        $this->addGet('/editori/settimana/', ['action' => 'weeklyEditors']);
        $this->addGet('/editori/mese/', ['action' => 'monthlyEditors']);
        $this->addGet('/editori/trimestre/', ['action' => 'quarterlyEditors']);
        $this->addGet('/editori/anno/', ['action' => 'yearlyEditors']);
        $this->addGet('/editori/sempre/', ['action' => 'everEditors']);
    $this->addGet('/reporters/', ['action' => 'weeklyReporters']);
        $this->addGet('/reporters/settimana/', ['action' => 'weeklyReporters']);
        $this->addGet('/reporters/mese/', ['action' => 'monthlyReporters']);
        $this->addGet('/reporters/trimestre/', ['action' => 'quarterlyReporters']);
        $this->addGet('/reporters/anno/', ['action' => 'yearlyReporters']);
        $this->addGet('/reporters/sempre/', ['action' => 'everReporters']);
    $this->addGet('/bloggers/', ['action' => 'weeklyBloggers']);
        $this->addGet('/bloggers/settimana/', ['action' => 'weeklyBloggers']);
        $this->addGet('/bloggers/mese/', ['action' => 'monthlyBloggers']);
        $this->addGet('/bloggers/trimestre/', ['action' => 'quarterlyBloggers']);
        $this->addGet('/bloggers/anno/', ['action' => 'yearlyBloggers']);
        $this->addGet('/bloggers/sempre/', ['action' => 'everBloggers']);
    $this->addGet('/moderatori/', ['action' => 'moderators']);
    $this->addGet('/privilegi/', ['action' => 'privileges']);
  }

}