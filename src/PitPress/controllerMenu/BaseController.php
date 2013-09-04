<?php

//! @file BaseController.php
//! @brief Ancestor of every defined controller.
//! @details Here you can find the common functions of each controller.
//! @author Filippo F. Fadda


//! @brief PitPress controllers namespace.
namespace PitPress\Controller;


use Phalcon\Tag;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;


//! @brief The base controller, a subclass of Phalcon controller.
//! @nosubgrouping
abstract class BaseController extends Controller {
  protected $couch;
  protected $redis;

  // Stores the main menu definition.
  protected static $controllerMenu = [
    ['link' => '/', 'name' => 'P.IT', 'icon' => 'home'],
    ['link' => '/blog/', 'name' => 'BLOG', 'icon' => 'code'],
    ['link' => '/domande/', 'name' => 'FORUM', 'icon' => 'question'],
    ['link' => '/links/', 'name' => 'LINKS', 'icon' => 'link'],
    ['link' => '/tags/', 'name' => 'TAGS', 'icon' => 'tags'],
    ['link' => '/badges/', 'name' => 'BADGES', 'icon' => 'certificate'],
    ['link' => '/utenti/', 'name' => 'UTENTI', 'icon' => 'group']
  ];


  //! @brief Initializes the controller.
  public function initialize() {
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];

    $this->view->setVar('controllerPath', static::$controllerPath);
    $this->view->setVar('controllerMenu', self::$controllerMenu);
    $this->view->setVar('controllerIndex', static::$controllerIndex);
  }

}