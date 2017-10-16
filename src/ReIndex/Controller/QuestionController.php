<?php

/**
 * @file QuestionController.php
 * @brief This file contains the QuestionController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


use ToolBag\Helper;


/**
 * @brief Controller of Question actions.
 * @nosubgrouping
 */
final class QuestionController extends IndexController {


  /**
   * @copydoc IndexController::getLabel()
   */
  protected function getLabel() {
    return 'questions';
  }


  /**
   * @copydoc IndexController::popular()
   */
  protected function popular($filter, $unversionTagId = NULL) {
    $this->periods = Helper\ArrayHelper::slice($this->periods, 7);
    parent::popular($filter, $unversionTagId);
  }


  /**
   * @brief Displays the questions that are still open.
   * @param[in] string $filter (optional) A filter.
   */
  public function openAction($filter = 'new') {
    $filters = ['new' => NULL, 'popular' => NULL, 'active' => NULL];

    $filter = Helper\ArrayHelper::key($filter, $filters);
    if ($filter === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->dispatcher->setParam('filter', $filter);

    $this->view->setVar('filters', $filters);
    $this->view->setVar('entriesCount', Helper\TextHelper::formatNumber(0));
    $this->view->setVar('title', sprintf('Open %s', ucfirst($this->getLabel())));
  }


  /**
   * @brief Displays the questions that still open by tag.
   * @param[in] string $tag The tag name.
   * @param[in] string $filter (optional)
   */
  public function openByTagAction($tag, $filter = NULL) {
  }


    /**
   * @brief Creates a new question.
   */
  public function newAction() {
    parent::newAction();
  }

}