<?php

/**
 * @file ErrorController.php
 * @brief This file contains the ErrorController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


use Phalcon\Mvc\View;


/**
 * @brief Controller of Error actions.
 * @nosubgrouping
 */
final class ErrorController extends BaseController {


  /**
   * @brief 404 Not Found.
   */
  public function show404Action() {
    $this->response->setHeader('HTTP/1.0 404', 'Not Found');
    $this->view->setVar('code', '404');
    $this->view->setVar('title', 'Page not found');
    $this->view->setVar('message', 'The requested page cannot be found. The link you have followed is not working or the page has been removed.');
    $this->view->setVar('method', strtolower($_SERVER['REQUEST_METHOD']));
    $this->view->setVar('url', "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
    $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
    $this->view->pick('views/error/basic');
  }


  /**
   * @brief 401 Forbidden.
   */
  public function show401Action() {
    $this->response->setHeader('HTTP/1.0 401', 'Forbidden');
    $this->view->setVar('code', '401');
    $this->view->setVar('title', 'Operation denied');
    $this->view->setVar('message', "The requested action is prohibited.");
    $this->view->setVar('method', strtolower($_SERVER['REQUEST_METHOD']));
    $this->view->setVar('url', "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
    $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
    $this->view->pick('views/error/basic');
  }


  /**
   * @brief 503 Service Unavailable.
   */
  public function show503Action() {
    $this->response->setHeader('HTTP/1.0 503', 'Service Unavailable');
    $this->view->setVar('code', '401');
    $this->view->setVar('title', 'The service is not available');
    $this->view->setVar('message', "Site in maintenance, the service is temporarily unavailable. Please, try again later.");
    $this->view->setVar('method', strtolower($_SERVER['REQUEST_METHOD']));
    $this->view->setVar('url', "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
    $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
    $this->view->pick('views/error/basic');
  }

}