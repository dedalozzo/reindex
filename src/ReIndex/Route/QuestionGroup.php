<?php

/**
 * @file QuestionGroup.php
 * @brief This file contains the QuestionGroup class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Route;


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
    return '/questions';
  }


  protected function addRoutes($postfix = "") {
    $this->addGet('/', ['action' => $this->getDefaultAction().$postfix]);
    $this->addGet('/new/', ['action' => 'newest'.$postfix]);
    $this->addGet('/popular/', ['action' => 'popular'.$postfix]);
    $this->addGet('/popular/{filter}/', ['action' => 'popular'.$postfix]);
    $this->addGet('/active/', ['action' => 'active'.$postfix]);
    $this->addGet('/open/', ['action' => 'open'.$postfix]);
    $this->addGet('/open/{filter}/', ['action' => 'open'.$postfix]);
    $this->addGet('/favorites/', ['action' => 'favorite'.$postfix]);
    $this->addGet('/favorites/{filter}/', ['action' => 'favorite'.$postfix]);

    $this->addGet('/{year:[0-9]{4}}/', ['action' => 'perDate'.$postfix]);
    $this->addGet('/{year:[0-9]{4}}/{month:[0-9]{2}}/', ['action' => 'perDate'.$postfix]);
    $this->addGet('/{year:[0-9]{4}}/{month:[0-9]{2}}/{day:[0-9]{2}}/', ['action' => 'perDate'.$postfix]);
    //$this->addGet('/([0-9]{4})/', ['action' => 'perDate', 'year' => 1]);
    //$this->addGet('/([0-9]{4})/([0-9]{2})/', ['action' => 'perDate', 'year' => 1, 'month' => 2]);
    //$this->addGet('/([0-9]{4})/([0-9]{2})/([0-9]{2})/', ['action' => 'perDate', 'year' => 1, 'month' => 2, 'day' => 3]);
    //$this->addGet('/([0-9]{4})/(?:([0-9]{2})/(?:([0-9]{2})/){0,1}){0,1}', ['action' => 'perDate', 'year' => 1, 'month' => 2, 'day' => 3]);

    //$this->addGet('/rss', ['action' => 'rss']);
  }

} 