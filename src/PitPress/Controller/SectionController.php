<?php

//! @file SectionController.php
//! @brief This file contains the SectionController class.
//! @details
//! @author Filippo F. Fadda



namespace PitPress\Controller;


//! @brief Ancestor controller for any section controller.
//! @nosubgrouping
abstract class SectionController extends BaseController {


  //! @brief Given a set of keys, retrieves entries.
  abstract protected function getEntries($keys);


  //! @brief Returns an associative array of titles indexed by action name.
  protected static function getTitles() {
    return array_column(static::$sectionMenu, 'title', 'name');
  }


  public function initialize() {
    parent::initialize();
  }


  public function beforeExecuteRoute() {
    parent::beforeExecuteRoute();

    $this->view->setVar('actionPath', self::getPaths(static::$sectionMenu)[$this->actionName]);
    $this->view->setVar('title', self::getTitles()[$this->actionName]);
  }


  public function afterExecuteRoute() {
    parent::afterExecuteRoute();

    $this->view->setVar('sectionLabel', static::$sectionLabel);
    $this->view->setVar('sectionMenu', static::$sectionMenu);
  }

}