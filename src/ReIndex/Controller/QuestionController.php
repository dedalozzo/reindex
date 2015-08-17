<?php

/**
 * @file QuestionController.php
 * @brief This file contains the QuestionController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


use ReIndex\Helper;


/**
 * @brief Controller of Question actions.
 * @nosubgrouping
 */
class QuestionController extends IndexController {


  /**
   * @copydoc IndexController::getLabel()
   */
  protected function getLabel() {
    return 'questions';
  }


  /**
   * @copydoc BaseController::getPeriod()
   */
  protected function getPeriod($filter) {
    return empty($filter) ? Helper\Time::THIS_WEEK : Helper\ArrayHelper::value($filter, $this->periods);
  }


  /**
   * @copydoc IndexController::popular()
   */
  protected function popular($filter, $unversionTagId = NULL) {
    $this->periods = Helper\ArrayHelper::slice($this->periods, 7);
    parent::popular($filter);
  }


  /**
   * @copydoc IndexController::newestAction()
   */
  public function newestAction($tag = NULL) {
    parent::newestAction();
    $this->view->setVar('title', sprintf('New %s', $this->getLabel()));
  }


  /**
   * @copydoc IndexController::activeAction()
   */
  public function activeAction() {
    parent::activeAction();
    $this->view->setVar('title', sprintf('Active %s', ucfirst($this->getLabel())));
  }


  /**
   * @brief Displays the newest questions having a bounty.
   */
  public function importantAction() {
    $this->view->setVar('entriesCount', 0);
    $this->view->setVar('title', sprintf('Important %s', ucfirst($this->getLabel())));
  }


  /**
   * @brief Displays the questions, still open, based on user's tags.
   * @param[in] string $filter (optional) A filter.
   */
  public function openAction($filter = NULL) {
    // Stores sub-menu definition.
    $filters = ['no-answer' => 0, 'popular' => 1, 'new' => 2];
    if (is_null($filter)) $filter = 'nessuna-risposta';

    $index = Helper\ArrayHelper::value($filter, $filters);
    if ($index === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->view->setVar('entriesCount', 0);
    $this->view->setVar('submenu', $filters);
    $this->view->setVar('submenuIndex', $index);
    $this->view->setVar('title', sprintf('Open %s', ucfirst($this->getLabel())));
  }


  /**
   * @brief Creates a new question.
   */
  public function newAction() {
    parent::newAction();
  }

}