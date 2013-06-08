<?php

//! @file BlogController.php
//! @brief Controller of Blog actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


class BlogController extends BaseController {

  public function initialize() {
    \Phalcon\Tag::setTitle('Getting Help');
    parent::initialize();
  }

  public function recentsAction() {
  }


  public function popularsAction() {
  }


  public function basedOnMyTagsAction() {
  }


  public function mostVotedAction() {
  }


  public function mostDiscussedAction() {
  }


  public function writtenByMeAction() {
  }


  public function rssAction() {
  }

}