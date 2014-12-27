<?php

/**
 * @file FooterController.php
 * @brief This file contains the FooterController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


use Phalcon\Mvc\View;


/**
 * @brief Controller of Footer actions.
 * @nosubgrouping
 */
class FooterController extends BaseController {

  /**
   * @brief Displays the tour page.
   */
  public function tourAction() {
    return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Displays the help page.
   */
  public function helpAction() {
    return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Displays a page with the legal info.
   */
  public function legalAction() {
    //phpinfo();
    return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Displays the privacy page.
   */
  public function privacyAction() {
    return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Displays the career page.
   */
  public function careerAction() {
    return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Displays the advertising page.
   */
  public function advertisingAction() {
    return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Displays the contacts page.
   */
  public function contactAction() {
    return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }

}