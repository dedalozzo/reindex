<?php

//! @file QuestionsGroup.php
//! @brief Group of Questions routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;


//! @brief Group of questions routes.
//! @nosubgrouping
class QuestionsGroup extends Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'questions'
      ]);

    $this->setHostName('domande.programmazione.me');

    $this->addGet('/', ['action' => 'important']);
    $this->addGet('/nuove/', ['action' => 'newest']);
    $this->addGet('/importanti/', ['action' => 'important']);
    $this->addGet('/popolari/{period}', ['action' => 'popular']);
    $this->addGet('/aggiornate/', ['action' => 'updated']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);
    $this->addGet('/aperte/{type}', ['action' => 'open']);

    $this->addGet('/rss', ['action' => 'rss']);
  }

}