<?php

/**
 * @file Article.php
 * @brief This file contains the Article class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Security\Permission\Revision\Post\Article as Permission;
use ReIndex\Task\IndexPostTask;
use ReIndex\Exception;


/**
 * @brief This class represents a blog article.
 * @nosubgrouping
 */
final class Article extends Post {


  /**
   * @copydoc Revision::instantApproval()
   */
  protected function instantApproval() {
    return $this->user->has(new Permission\ApprovePermission($this));
  }


  /**
   * @copydoc Revision::approve()
   */
  public function approve() {
    $this->castVoteForPeerReview(new Permission\ApprovePermission($this));
  }


  /**
   * @copydoc Revision::reject()
   */
  public function reject($reason) {
    $this->castVoteForPeerReview(new Permission\RejectPermission($this), $reason);
  }


  /**
   * @brief Imports the article into the database.
   * @details This function postpones the metadata refresh. It is used in place of `submit()` to import the articles.
   * The refresh is executed in a task by a pool of processes, increasing the general importing performances.
   */
  public function import() {
    if (!$this->user->has(new Permission\ImportPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    $this->state->set(State::IMPORTED);
    $this->tasks->add(new IndexPostTask($this));
  }

}