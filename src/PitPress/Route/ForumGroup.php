<?php

//! @file ForumGroup.php
//! @brief Group of Forum routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


class ForumGroup extends \Phalcon\Mvc\Router\Group {

  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'forum'
      ]);

    // All the routes start with /domande.
    $this->setPrefix('/domande');

    $this->addGet('/', ['action' => 'noAnswer']);
    $this->addGet('/recenti', ['action' => 'recents']);
    $this->addGet('/senza-risposta', ['action' => 'noAnswer']);
    $this->addGet('/poste-da-me', ['action' => 'madeByMe']);
    $this->addGet('/rivolte-a-me', ['action' => 'askedToMe']);
    $this->addGet('/aperte', ['action' => 'opened']);
    $this->addGet('/attive', ['action' => 'active']);
    $this->addGet('/piu-votate', ['action' => 'mostVoted']);
    $this->addGet('/a-cui-risposi', ['action' => 'answeredByMe']);
    $this->addGet('/rss', ['action' => 'rss']);
  }
}