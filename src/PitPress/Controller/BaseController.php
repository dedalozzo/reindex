<?php

//! @file BaseController.php
//! @brief Ancestor of every defined controller.
//! @details Here you can find the common functions of each controller.
//! @author Filippo F. Fadda


//! @brief PitPress controllers namespace.
namespace PitPress\Controller;


use Phalcon\Mvc\Controller;

use PitPress\Factory\UserFactory;


//! @brief The base controller, a subclass of Phalcon controller.
//! @nosubgrouping
abstract class BaseController extends Controller {
  const VERSION = '7.0';

  protected $couch;
  protected $redis;
  protected $monolog;

  protected $serverName;
  protected $baseUri;
  protected $controllerName;
  protected $actionName;

  protected $user;

  // Stores the main menu definition.
  protected static $mainMenu = [
    ['name' => 'index', 'path' => '', 'label' => 'P.IT', 'icon' => 'home'],
    ['name' => 'questions', 'path' => 'domande.', 'label' => 'DOMANDE', 'icon' => 'question'],
    ['name' => 'links', 'path' => 'links.', 'label' => 'LINKS', 'icon' => 'link'],
    ['name' => 'blog', 'path' => 'blog.', 'label' => 'BLOG', 'icon' => 'code'],
    ['name' => 'tags', 'path' => 'tags.', 'label' => 'TAGS', 'icon' => 'tags'],
    ['name' => 'badges', 'path' => 'badges.', 'label' => 'BADGES', 'icon' => 'certificate'],
    ['name' => 'users', 'path' => 'utenti.', 'label' => 'UTENTI', 'icon' => 'group']
  ];


  //! @brief Returns an associative array of paths indexed by controller name.
  protected static function getPaths($menu) {
    return array_column($menu, 'path', 'name');
  }

  //! @brief Redirects to the home specified uri. In case an uri is not provided, the function redirects to the home page.
  //! @param[in] string $uri The redirect URI.
  protected function redirect($uri = "") {
    if (empty($uri))
      return $this->response->redirect($this->baseUri, TRUE);
    else
      return $this->response->redirect($uri, TRUE);
  }


  //! @brief Initializes the controller.
  public function initialize() {
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
    $this->monolog = $this->di['monolog'];

    $this->serverName = $this->di['config']['application']['serverName'];
    $this->baseUri = "http://".$this->serverName;
    $this->controllerName = $this->dispatcher->getControllerName();

    $this->user = UserFactory::getFromCookie();
    if (isset($this->user))
      $this->view->setVar('user', $this->user);

    // The main menu is present in every page.
    $this->view->setVar('mainMenu', self::$mainMenu);

    $this->view->setVar('year', date('Y'));
    $this->view->setVar('version', self::VERSION);
    $this->view->setVar('serverName', $this->serverName);
    $this->view->setVar('baseUri', $this->baseUri);
    $this->view->setVar('controllerName', $this->controllerName);
    $this->view->setVar('controllerPath', 'http://'.self::getPaths(self::$mainMenu)[$this->controllerName].$this->serverName);
  }


  //! @brief This method is executed before the initialize. In my opinion it's a bug.
  //! @details Cannot log inside this method using the monolog instance.
  public function beforeExecuteRoute() {
    $this->actionName = $this->dispatcher->getActionName();
    $this->view->setVar('actionName', $this->actionName);
  }


  public function afterExecuteRoute() {
  }


  public function notFoundAction() {
    $this->response->setHeader(404 , 'Not Found');
    $this->view->pick('404/404');
  }

}