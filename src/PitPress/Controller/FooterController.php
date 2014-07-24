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
class FooterController {

  /**
   * @brief Displays the tour page.
   */
  public function tourAction() {
    phpinfo();
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Displays the help page.
   */
  public function helpAction() {
    phpinfo();
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Displays a page with the legal info.
   */
  public function legalAction() {
    phpinfo();
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Displays the privacy page.
   */
  public function privacyAction() {
    phpinfo();
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Displays the career page.
   */
  public function careerAction() {
    phpinfo();
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Displays the advertising page.
   */
  public function advertisingAction() {
    phpinfo();
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Displays the contacts page.
   */
  public function contactAction() {
    phpinfo();
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }

}