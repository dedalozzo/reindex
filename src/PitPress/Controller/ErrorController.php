<?php

/**
 * @file ErrorController.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


class ErrorController extends BaseController {


  public function show404Action() {
    $this->response->setHeader(404, 'Not Found');
    $this->view->pick('404/404');
  }

} 