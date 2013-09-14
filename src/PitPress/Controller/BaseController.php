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

  protected $serverName;
  protected $baseUri;
  protected $controllerName;
  protected $actionName;


  // Stores the main menu definition.
  protected static $mainMenu = [
    ['name' => 'index', 'path' => '', 'label' => 'P.IT', 'icon' => 'home'],
    ['name' => 'blog', 'path' => 'blog.', 'label' => 'BLOG', 'icon' => 'code'],
    ['name' => 'forum', 'path' => 'forum.', 'label' => 'FORUM', 'icon' => 'question'],
    ['name' => 'links', 'path' => 'links.', 'label' => 'LINKS', 'icon' => 'link'],
    ['name' => 'tags', 'path' => 'tags.', 'label' => 'TAGS', 'icon' => 'tags'],
    ['name' => 'badges', 'path' => 'badges.', 'label' => 'BADGES', 'icon' => 'certificate'],
    ['name' => 'users', 'path' => 'utenti.', 'label' => 'UTENTI', 'icon' => 'group']
  ];


  // Returns an associative array of paths indexed by controller name.
  protected static function getPaths($menu) {
    return array_column($menu, 'path', 'name');
  }


  //! @brief Initializes the controller.
  public function initialize() {
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];

    $this->serverName = $this->di['config']['application']['serverName'];
    $this->baseUri = "http://".$this->serverName;
    $this->controllerName = $this->dispatcher->getControllerName();

    // The main menu is present in every page.
    $this->view->setVar('mainMenu', self::$mainMenu);

    $this->view->setVar('serverName', $this->serverName);
    $this->view->setVar('baseUri', $this->baseUri);
    $this->view->setVar('controllerName', $this->controllerName);
    $this->view->setVar('controllerPath', 'http://'.self::getPaths(self::$mainMenu)[$this->controllerName].$this->serverName);
  }


  public function afterExecuteRoute() {
    $this->actionName = $this->dispatcher->getActionName();

    $this->view->setVar('actionName', $this->actionName);
    $this->view->setVar('actionPath', self::getPaths(static::$sectionMenu)[$this->actionName]);
  }

}