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


  public function initialize() {
    parent::initialize();
  }


  public function beforeExecuteRoute() {
    parent::beforeExecuteRoute();
  }


  public function afterExecuteRoute() {
    parent::afterExecuteRoute();
  }

}