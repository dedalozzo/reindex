<?php

//! @file BaseController.php
//! @brief Ancestor of every defined controller.
//! @details Here you can find the common functions of each controller.
//! @author Filippo F. Fadda


//! @brief PitPress controllers namespace.
namespace PitPress\Controller;


use Phalcon\Mvc\Controller;


//! @brief The base controller, a subclass of Phalcon controller.
//! @nosubgrouping
abstract class BaseController extends Controller {
  protected $couch;
  protected $redis;

  // Stores the main menu definition.
  protected static $mainMenu = [
    ['name' => 'index', 'link' => '/', 'label' => 'P.IT', 'icon' => 'home'],
    ['name' => 'blog', 'link' => '/blog/', 'label' => 'BLOG', 'icon' => 'code'],
    ['name' => 'forum', 'link' => '/domande/', 'label' => 'FORUM', 'icon' => 'question'],
    ['name' => 'links', 'link' => '/links/', 'label' => 'LINKS', 'icon' => 'link'],
    ['name' => 'tags', 'link' => '/tags/', 'label' => 'TAGS', 'icon' => 'tags'],
    ['name' => 'badges', 'link' => '/badges/', 'label' => 'BADGES', 'icon' => 'certificate'],
    ['name' => 'users', 'link' => '/utenti/', 'label' => 'UTENTI', 'icon' => 'group']
  ];


  // Returns an associative array of paths indexed by controller name.
  protected static function getPaths($menu) {
    return array_column($menu, 'link', 'name');
  }


  //! @brief Initializes the controller.
  public function initialize() {
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];

    // The main menu is present in every page.
    $this->view->setVar('mainMenu', self::$mainMenu);

    $this->view->setVar('controllerName', $this->dispatcher->getControllerName());
    $this->view->setVar('controllerPath', self::getPaths(self::$mainMenu)[$this->dispatcher->getControllerName()]);
  }


  public function afterExecuteRoute() {
    $this->view->setVar('actionName', $this->dispatcher->getActionName());
    $this->view->setVar('actionPath', self::getPaths(static::$sectionMenu)[$this->dispatcher->getActionName()]);
  }

}