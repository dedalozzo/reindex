<?php

/**
 * @file BaseController.php
 * @brief Ancestor of every defined controller.
 * @details Here you can find the common functions of each controller.
 * @author Filippo F. Fadda
 */


//! PitPress controllers namespace.
namespace PitPress\Controller;


use Phalcon\Mvc\Controller;
use PitPress\Version;
use PitPress\Factory\UserFactory;


/**
 * @brief The base controller, a subclass of Phalcon controller.
 * @nosubgrouping
 */
abstract class BaseController extends Controller {
  protected $couch;
  protected $redis;
  protected $monolog;

  protected $baseUri;
  protected $domainName;
  protected $controllerName;
  protected $actionName;

  protected $user;


  /**
   * @brief Returns an associative array of paths indexed by controller name.
   */
  protected static function getPaths($menu) {
    return array_column($menu, 'path', 'name');
  }


  /**
   * @brief Redirects to the home specified uri. In case an uri is not provided, the function redirects to the home page.
   * @param[in] string $uri The redirect URI.
   */
  protected function redirect($uri = "") {
    if (empty($uri))
      return $this->response->redirect($this->baseUri, TRUE);
    else
      return $this->response->redirect($uri, TRUE);
  }


  /**
   * @brief Initializes the controller.
   */
  public function initialize() {
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
    $this->monolog = $this->di['monolog'];

    // Used in subclasses also.
    $this->domainName = $this->di['config']['application']['domainName'];

    $this->baseUri = "//".$this->domainName;

    $this->user = UserFactory::getFromCookie();
  }


  /**
   * @brief This method is executed before the initialize. In my opinion it's a bug.
   * @details Cannot log inside this method using the monolog instance.
   */
  public function beforeExecuteRoute() {
  }


  public function afterExecuteRoute() {
    $this->view->setVar('year', date('Y'));

    $this->view->setVar('version', Version::getNumber());

    $this->view->setVar('domainName', $this->domainName);
    $this->view->setVar('serverName', $_SERVER['SERVER_NAME']);
    $this->view->setVar('controllerName', $this->dispatcher->getControllerName());
    $this->view->setVar('actionName', $this->dispatcher->getActionName());

    $this->view->setVar('baseUri', $this->baseUri);

    if (isset($this->user))
      $this->view->setVar('currentUser', $this->user);
  }


  public function notFoundAction() {
    $this->response->setHeader(404 , 'Not Found');
    $this->view->pick('404/404');
  }

}