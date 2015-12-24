<?php

/**
 * @file BaseController.php
 * @brief This file contains the BaseController class.
 * @author Filippo F. Fadda
 */


//! Controllers
namespace ReIndex\Controller;


use Phalcon\Mvc\Controller;

use ReIndex\Helper;
use ReIndex\Version;
use ReIndex\Model\User;


/**
 * @brief The base controller, a subclass of Phalcon controller.
 * @details Here you can find the common functions of each controller.
 * @nosubgrouping
 */
abstract class BaseController extends Controller {
  protected $config;
  protected $couch;
  protected $redis;
  protected $log;
  protected $assets;
  protected $guardian;

  protected $domainName;
  protected $serverName;
  protected $controllerName;
  protected $actionName;

  protected $user;


  /**
   * @brief Sets the referrer is any.
   */
  protected function setReferrer() {
    $requestUri = "//".$this->domainName.$_SERVER['REQUEST_URI'];

    // Sets the HTTP Referrer to be able to return to the previous page.
    if (isset($_SERVER['HTTP_REFERER']))
      $referrerUri = $_SERVER['HTTP_REFERER'];
    else
      $referrerUri = "";

    if (!empty($referrerUri) && ($requestUri != $referrerUri))
      $this->session->set("referrer", $referrerUri);
    else
      $this->session->remove("referrer");
  }


  /**
   * @brief Redirects to the referrer page if any.
   * @param[in] User $user (optional) The current user if any.
   */
  protected function redirectToReferrer(User $user = NULL) {
    if ($this->session->has("referrer"))
      return $this->response->redirect($this->session->get("referrer"), TRUE);
    elseif (isset($user))
      return $this->redirect("http://" . $this->domainName . "/" . $user->username);
    else
      return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
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
    $this->config = $this->di['config'];
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
    $this->log = $this->di['log'];
    $this->assets = $this->di['assets'];
    $this->guardian = $this->di['guardian'];

    $this->user = $this->guardian->getUser();

    // It is just the primary domain, for example: `reindex.xyz`.
    $this->domainName = $this->di['config']['application']['domainName'];

    // Includes the subdomain if any, for example: `it-it.reindex.xyz`.
    $this->serverName = $_SERVER['SERVER_NAME'];

    $this->controllerName = $this->dispatcher->getControllerName();
    $this->actionName = $this->dispatcher->getActionName();

    $this->themeName = $this->di['config']['application']['themeName'];
    $this->dist = "/reindex/themes/".$this->themeName."/dist";

    // Includes the common assets.
    // Remember to never include the assets in the `afterExecuteRoute()` method!
    $this->assets->addCss($this->dist."/css/reboot.css", FALSE);
    $this->assets->addJs("//cdnjs.cloudflare.com/ajax/libs/jquery/".$this->di['config']['assets']['jQueryVersion']."/jquery.min.js", FALSE);
    $this->assets->addJs($this->dist."/js/dropdown.min.js", FALSE);
  }


  /**
   * @brief This method is executed before the initialize. In my opinion it's a bug.
   * @details Cannot log inside this method using the log instance.
   * @warning Do not use it!
   */
  public function beforeExecuteRoute() {}


  public function afterExecuteRoute() {
    $this->view->setVar('year', date('Y'));

    $this->view->setVar('version', Version::getNumber());

    $this->view->setVar('user', $this->user);
    $this->view->setVar('domainName', $this->domainName);
    $this->view->setVar('serverName', $this->serverName);
    $this->view->setVar('controllerName', $this->controllerName);
    $this->view->setVar('actionName', $this->actionName);

    $this->view->setVar('dist', $this->dist);

    //$this->log->addDebug(sprintf("Controller: %s", $this->dispatcher->getControllerName()));
    //$this->log->addDebug(sprintf("Action: %s", $this->dispatcher->getActionName()));
  }


  public function notFoundAction() {
    $this->response->setHeader(404 , 'Not Found');
    $this->view->pick('404/404');
  }

}