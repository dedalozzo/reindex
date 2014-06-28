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


/**
 * @brief The base controller, a subclass of Phalcon controller.
 * @nosubgrouping
 */
abstract class BaseController extends Controller {
  protected $couch;
  protected $redis;
  protected $monolog;

  protected $domainName;
  protected $serverName;
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
      return $this->response->redirect("//".$this->domainName, TRUE);
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

    $this->guardian = $this->di['guardian'];
    $this->user = $this->guardian->getCurrentUser();

    // It is just the primary domain, for example: `programmazione.it`.
    $this->domainName = $this->di['config']['application']['domainName'];

    // Includes the subdomain if any, for example: `blog.programmazione.it`.
    $this->serverName = $_SERVER['SERVER_NAME'];
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
    $this->view->setVar('serverName', $this->serverName);
    $this->view->setVar('controllerName', $this->dispatcher->getControllerName());
    $this->view->setVar('actionName', $this->dispatcher->getActionName());

    if (isset($this->user))
      $this->view->setVar('currentUser', $this->user);
  }


  public function notFoundAction() {
    $this->response->setHeader(404 , 'Not Found');
    $this->view->pick('404/404');
  }

}