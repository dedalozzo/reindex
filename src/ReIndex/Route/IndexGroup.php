<?php

/**
 * @file IndexGroup.php
 * @brief This file contains the IndexGroup class.
 * @details
 * @author Filippo F. Fadda
 */


//! Groups of routes
namespace ReIndex\Route;


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
   * @brief Adds the routes to the group.
   * @details This method maybe called twice because tags use the same routes with a different prefix.
   */
  protected function addRoutes($postfix = "") {
    $this->addGet('/', ['action' => $this->getDefaultAction().$postfix]);
    $this->addGet('/new/', ['action' => 'newest'.$postfix]);
    $this->addGet('/active/', ['action' => 'active'.$postfix]);
    $this->addGet('/popular/', ['action' => 'popular'.$postfix]);
    $this->addGet('/popular/{filter}/', ['action' => 'popular'.$postfix]);

    $this->addGet('/{year:[0-9]{4}}/', ['action' => 'perDate'.$postfix]);
    $this->addGet('/{year:[0-9]{4}}/{month:[0-9]{2}}/', ['action' => 'perDate'.$postfix]);
    $this->addGet('/{year:[0-9]{4}}/{month:[0-9]{2}}/{day:[0-9]{2}}/', ['action' => 'perDate'.$postfix]);
    //$this->addGet('/([0-9]{4})/', ['action' => 'perDate', 'year' => 1]);
    //$this->addGet('/([0-9]{4})/([0-9]{2})/', ['action' => 'perDate', 'year' => 1, 'month' => 2]);
    //$this->addGet('/([0-9]{4})/([0-9]{2})/([0-9]{2})/', ['action' => 'perDate', 'year' => 1, 'month' => 2, 'day' => 3]);
    //$this->addGet('/([0-9]{4})/(?:([0-9]{2})/(?:([0-9]{2})/){0,1}){0,1}', ['action' => 'perDate', 'year' => 1, 'month' => 2, 'day' => 3]);
  }


  public function initialize() {
    $this->setPaths(['namespace' => 'ReIndex\Controller', 'controller' => $this->getController()]);
    $this->setHostName(DI::getDefault()['config']['application']['domainName']);

    // Sets the standard routes for a tag. Don't change the order!
    $this->setPrefix('/{tag:[a-z0-9.-]+}'.$this->getPrefix());
    $this->addRoutes('ByTag');

    // Sets the standard routes.
    $this->setPrefix($this->getPrefix());
    $this->addRoutes();

    // The following routes don't support the postfix.
    $this->addGet('/info/', ['action' => 'infoByTag']);
    $this->addGet('/interesting/', ['action' => 'interesting']);
    $this->addGet('/favorites/', ['action' => 'favorite']);
    $this->addGet('/favorites/{filter}/', ['action' => 'favorite']);
    $this->add('/add', ['action' => 'add'])->via(['GET', 'POST']);
  }

}