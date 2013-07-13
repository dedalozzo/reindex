<?php

//! @file TagsController.php
//! @brief Controller of Tags actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


//! @brief Controller of Tags actions.
//! @nosubgrouping
class TagsController extends BaseController {

  public function initialize() {
    \Phalcon\Tag::setTitle('Getting Help');
    parent::initialize();
  }


  public function popularsAction() {
  }


  public function byNameAction() {
  }


  public function recentsAction() {
  }

}