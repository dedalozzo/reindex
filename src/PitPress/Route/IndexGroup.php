<?php

/**
 * @file IndexGroup.php
 * @brief This file contains the IndexGroup class.
 * @details
 * @author Filippo F. Fadda
 */


//! PitPress routes namespace.
namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;
use Phalcon\DI;


/**
 * @brief Group of Index routes.
 * @nosubgrouping
 */
class IndexGroup extends Group {


  /**
   * @brief Returns the controller name.
   */
  protected function getController() {
    return 'index';
  }


  /**
   * @brief Returns the default action name.
   */
  protected function getDefaultAction() {
    return 'index';
  }


  /**
   * @brief Returns the routes prefix.
   */
  public function getPrefix() {
    return '';
  }


  /**
   * @brief Page index.
   */
  protected function addRoutes() {
    $this->setPrefix($this->getPrefix());
    $this->addGet('/', ['action' => $this->getDefaultAction()]);
    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/popolari/', ['action' => 'popular']);
    $this->addGet('/popolari/{filter}/', ['action' => 'popular']);
    $this->addGet('/attivi/', ['action' => 'active']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);
    $this->addGet('/preferiti/', ['action' => 'favorite']);
    $this->addGet('/preferiti/{filter}/', ['action' => 'favorite']);

    $this->addGet('/([0-9]{4})/', ['action' => 'perDate', 'year' => 1]);
    $this->addGet('/([0-9]{4})/([0-9]{2})/', ['action' => 'perDate', 'year' => 1, 'month' => 2]);
    $this->addGet('/([0-9]{4})/([0-9]{2})/([0-9]{2})/', ['action' => 'perDate', 'year' => 1, 'month' => 2, 'day' => 3]);
    //$this->addGet('/([0-9]{4})/(?:([0-9]{2})/(?:([0-9]{2})/){0,1}){0,1}', ['action' => 'perDate', 'year' => 1, 'month' => 2, 'day' => 3]);

    $this->setPrefix('/tags/{tag}'.$this->getPrefix());
    $this->addGet('', ['action' => 'newest']);
    $this->addGet('/nuovi/', ['action' => 'newest']);
    $this->addGet('/popolari/', ['action' => 'popular']);
    $this->addGet('/popolari/{filter}/', ['action' => 'popular']);
    $this->addGet('/attivi/', ['action' => 'active']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);
    $this->addGet('/preferiti/', ['action' => 'favorite']);
    $this->addGet('/preferiti/{filter}/', ['action' => 'favorite']);

    //$this->addGet('/rss', ['action' => 'rss']);
  }


  public function initialize() {
    $this->setPaths(['namespace' => 'PitPress\Controller', 'controller' => $this->getController()]);
    $this->setHostName(DI::getDefault()['config']['application']['domainName']);
    $this->addRoutes();
  }

}