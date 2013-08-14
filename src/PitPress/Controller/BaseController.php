<?php

//! @file BaseController.php
//! @brief Ancestor of every defined controller.
//! @details Here you can find the common functions of each controller.
//! @author Filippo F. Fadda


//! @brief PitPress controllers namespace.
namespace PitPress\Controller;


use Phalcon\Tag;
use Phalcon\Mvc\Controller;


//! @brief The base controller, a subclass of Phalcon controller.
//! @nosubgrouping
class BaseController extends Controller {
  const TITLE = 'PROGRAMMAZIONE.IT';

  protected $couch;

  //! @brief Initializes the controller.
  public function initialize() {
    $this->couch = $this->di['couchdb'];

    Tag::prependTitle('P.it | ');
  }

}