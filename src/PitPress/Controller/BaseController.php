<?php

/**
 * @file BaseController.php
 * @brief This file contains the BaseController class.
 * @author Filippo F. Fadda
 */


//! PitPress controllers namespace.
namespace PitPress\Controller;


use Phalcon\Mvc\Controller;

use PitPress\Helper;
use PitPress\Version;


/**
 * @brief The base controller, a subclass of Phalcon controller.
 * @details Here you can find the common functions of each controller.
 * @nosubgrouping
 */
abstract class BaseController extends Controller {
  protected $couch;
  protected $redis;
  protected $monolog;
  protected $assets;

  protected $domainName;
  protected $serverName;
  protected $controllerName;
  protected $actionName;

  protected $user;

  protected $periods = [
    "sempre" => Helper\Time::EVER,
    "anno scorso" => Helper\Time::YEAR,
    "quest'anno" => Helper\Time::YEAR,
    "mese scorso" => Helper\Time::MONTH,
    "questo mese" => Helper\Time::MONTH,
    "sett. scorsa" => Helper\Time::WEEK,
    "questa sett." => Helper\Time::WEEK,
    "ieri" => Helper\Time::DAY,
    "oggi" => Helper\Time::DAY
  ];


  /**
   * @brief Given a a human readable period of time, returns the correspondent number.
   * @param[in] string $filter A human readable period of time.
   * @return int|bool If the filter exists returns its number, else returns `false`.
   */
  protected function getPeriod($filter) {
    return is_null($filter) ? Helper\Time::EVER : Helper\ArrayHelper::value($filter, $this->periods);
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
    $this->assets = $this->di['assets'];

    $this->guardian = $this->di['guardian'];
    $this->user = $this->guardian->getCurrentUser();

    // It is just the primary domain, for example: `programmazione.it`.
    $this->domainName = $this->di['config']['application']['domainName'];

    // Includes the subdomain if any, for example: `it-it.programmazione.it`.
    $this->serverName = $_SERVER['SERVER_NAME'];

    $this->controllerName = $this->dispatcher->getControllerName();
    $this->actionName = $this->dispatcher->getActionName();

    // Includes the assets.
    $this->assets->addCss("/pit-bootstrap/dist/css/pit.css", FALSE);
    $this->assets->addJs("//cdnjs.cloudflare.com/ajax/libs/jquery/".$this->di['config']['assets']['jQueryVersion']."/jquery.min.js", FALSE);
    $this->assets->addJs("/pit-bootstrap/dist/js/dropdown.min.js", FALSE);
  }


  /**
   * @brief This method is executed before the initialize. In my opinion it's a bug.
   * @details Cannot log inside this method using the monolog instance.
   * @warning Do not use it!
   */
  public function beforeExecuteRoute() {}


  public function afterExecuteRoute() {
    $this->view->setVar('year', date('Y'));

    $this->view->setVar('version', Version::getNumber());

    $this->view->setVar('currentUser', $this->user);
    $this->view->setVar('domainName', $this->domainName);
    $this->view->setVar('serverName', $this->serverName);
    $this->view->setVar('controllerName', $this->controllerName);
    $this->view->setVar('actionName', $this->actionName);

    // Section and controller are different things. Many controllers, for example, belong to the same section. However,
    // in general, there is a one to one relation between controller and section; this is why we assign to the section
    // name, the controller name. Anyway, the section name can be overridden in child controllers, in case you want
    // associate a controller to a different section.
    $this->view->setVar('sectionName', $this->controllerName);

    $this->monolog->addDebug(sprintf("Controller: %s", $this->dispatcher->getControllerName()));
    $this->monolog->addDebug(sprintf("Action: %s", $this->dispatcher->getActionName()));

  }


  public function notFoundAction() {
    $this->response->setHeader(404 , 'Not Found');
    $this->view->pick('404/404');
  }

}