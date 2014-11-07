<?php

//! @file QuestionControl.php
//! @brief This file contains the QuestionController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use PitPress\Helper;


/**
 * @brief Controller of Question actions.
 * @nosubgrouping
 */
class QuestionController extends IndexController {


  protected function getLabel() {
    return 'domande';
  }


  protected function getPeriod($filter) {
    return empty($filter) ? Helper\Time::THIS_WEEK : Helper\ArrayHelper::value($filter, $this->periods);
  }


  protected function popular($filter, $unversionTagId = NULL) {
    $this->periods = Helper\ArrayHelper::slice($this->periods, 7);
    parent::popular($filter);
  }


  public function newestAction($tag = NULL) {
    parent::newestAction();
    $this->view->setVar('title', sprintf('Nuove %s', $this->getLabel()));
  }


  public function activeAction() {
    parent::activeAction();
    $this->view->setVar('title', sprintf('%s attive', ucfirst($this->getLabel())));
  }


  /**
   * @brief Displays the newest questions having a bounty.
   */
  public function importantAction() {
    $this->view->setVar('entriesCount', 0);
    $this->view->setVar('title', sprintf('%s importanti', ucfirst($this->getLabel())));
  }


  /**
   * @brief Displays the questions, still open, based on user's tags.
   */
  public function openAction($filter = NULL) {
    // Stores sub-menu definition.
    $filters = ['nessuna-risposta' => 0, 'popolari' => 1, 'nuove' => 2, 'rivolte-a-me' => 3];
    if (is_null($filter)) $filter = 'nessuna-risposta';

    $index = Helper\ArrayHelper::value($filter, $filters);
    if ($index === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->view->setVar('entriesCount', 0);
    $this->view->setVar('submenu', $filters);
    $this->view->setVar('submenuIndex', $index);
    $this->view->setVar('title', sprintf('%s aperte', ucfirst($this->getLabel())));
  }


  /**
   * @brief Creates a new question.
   */
  public function newAction() {
    parent::newAction();
  }

}