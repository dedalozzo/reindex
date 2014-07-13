<?php

/**
 * @file ErrorController.php
 * @brief This file contains the ErrorController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


use Phalcon\Mvc\View;


class ErrorController extends BaseController {


  public function show404Action() {
    $this->response->setHeader('HTTP/1.0 404', 'Not Found');
    $this->view->setVar('title', 'Pagina non trovata');
    $this->view->setVar('method', strtolower($_SERVER['REQUEST_METHOD']));
    $this->view->setVar('url', "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
    $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
    $this->view->pick('404/404');
  }

} 