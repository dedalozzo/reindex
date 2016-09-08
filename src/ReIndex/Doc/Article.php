<?php

/**
 * @file Article.php
 * @brief This file contains the Article class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Security\Permission\Versionable\Post\Article as Permission;
use ReIndex\Enum\State;
use Reindex\Exception;
use ReIndex\Controller\BaseController;


/**
 * @brief This class represents a blog article.
 * @nosubgrouping
 */
class Article extends Post {


  /**
   * @copydoc Versionable::approve()
   */
  public function approve() {
    $permission = new Permission\ApprovePermission($this);

    if (!$this->user->has($permission))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    $this->castVoteByRole($permission->getRole());

    $this->index();
  }


  /**
   * @copydoc Versionable::reject()
   */
  public function reject($reason) {
    $permission = new Permission\RejectPermission($this);

    if (!$this->user->has($permission))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    $this->castVoteByRole($permission->getRole());

    parent::reject($reason);
  }


  /**
   * @copydoc Versionable::revert()
   */
  public function revert($versionNumber = NULL) {
    if (!$this->user->has(new Permission\RevertPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    parent::revert($versionNumber);
  }


  /**
   * @copydoc Versionable::moveToTrash()
   */
  public function moveToTrash() {
    if (!$this->user->has(new Permission\MoveToTrashPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    parent::moveToTrash();
  }


  /**
   * @copydoc Versionable::restore()
   */
  public function restore() {
    if (!$this->user->has(new Permission\RestorePermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    parent::restore();
  }


  /**
   * @brief Marks the document as draft.
   * @details When a user works on an article, he wants save many time the item before submit it for peer revision.
   */
  public function saveAsDraft() {
    if (!$this->user->has(new Permission\SaveAsDraftPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    $this->state->set(State::DRAFT);

    // Used to group by year, month and day.
    $this->meta['year'] = date("Y", $this->createdAt);
    $this->meta['month'] = date("m", $this->createdAt);
    $this->meta['day'] = date("d", $this->createdAt);

    $this->save();
  }


  /**
   * @copydoc Post::editAction()
   */
  public function editAction(BaseController $controller) {
    if (!$this->user->has(new Permission\EditPermission($this)))
      return $controller->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    parent::editAction($controller);
  }

}