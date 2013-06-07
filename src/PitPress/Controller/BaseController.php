<?php

//! @file BaseController.php
//! @brief Ancestor of every defined controller.
//! @details Here you can find the common functions of each controller.
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use Phalcon\Tag;


class BaseController extends \Phalcon\Mvc\Controller {
  protected $couch;

  public function initialize() {
    $this->couch = $this->di['couchdb'];

    Tag::prependTitle('P.it | ');
  }

}