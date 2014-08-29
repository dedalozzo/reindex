<?php

//! @file QuestionGroup.php
//! @brief This file contains the QuestionGroup class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


use Phalcon\DI;


/**
 * @brief Group of Question routes.
 * @nosubgrouping
 */
class QuestionGroup extends IndexGroup {


  protected function getController() {
    return 'question';
  }


  protected function getDefaultAction() {
    return 'newest';
  }


  public function getPrefix() {
    return '/domande';
  }


  protected function addRoutes() {
    $this->addGet('/', ['action' => $this->getDefaultAction()]);
    $this->addGet('/nuove/', ['action' => 'newest']);
    $this->addGet('/popolari/', ['action' => 'popular']);
    $this->addGet('/popolari/{filter}/', ['action' => 'popular']);
    $this->addGet('/attive/', ['action' => 'active']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);
    $this->addGet('/importanti/', ['action' => 'important']);
    $this->addGet('/aperte/', ['action' => 'open']);
    $this->addGet('/aperte/{filter}/', ['action' => 'open']);
    $this->addGet('/preferite/', ['action' => 'favorite']);
    $this->addGet('/preferite/{filter}/', ['action' => 'favorite']);

    $this->addGet('/([0-9]{4})/', ['action' => 'perDate', 'year' => 1]);
    $this->addGet('/([0-9]{4})/([0-9]{2})/', ['action' => 'perDate', 'year' => 1, 'month' => 2]);
    $this->addGet('/([0-9]{4})/([0-9]{2})/([0-9]{2})/', ['action' => 'perDate', 'year' => 1, 'month' => 2, 'day' => 3]);
    //$this->addGet('/([0-9]{4})/(?:([0-9]{2})/(?:([0-9]{2})/){0,1}){0,1}', ['action' => 'perDate', 'year' => 1, 'month' => 2, 'day' => 3]);

    //$this->addGet('/rss', ['action' => 'rss']);
  }

} 