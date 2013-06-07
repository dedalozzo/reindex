<?php

//! @file NewsController.php
//! @brief Controller of News actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


class NewsController extends BaseController {

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


  public function postedByMeAction() {
  }


  public function rssAction() {
  }

}