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


  protected function addRoutes() {
    $this->setPaths(['namespace' => 'PitPress\Controller', 'controller' => 'question']);
    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    $this->setPrefix('/domande');
    $this->addGet('/', ['action' => 'newest']);
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

    $this->setPrefix('/tags/{tag}/domande');
    $this->addGet('', ['action' => 'newest']);
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

    //$this->addGet('/rss', ['action' => 'rss']);
  }

} 