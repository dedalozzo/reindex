<?php

/**
 * @file Question.php
 * @brief This file contains the Question class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Security\Permission\Post\Question as Permission;
use ReIndex\Enum\State;
use Reindex\Exception;
use ReIndex\Controller\BaseController;


/**
 * @brief A question asked by a member.
 * @nosubgrouping
 */
final class Question extends Post {

  /**
   * @copydoc Post::close()
   */
  public function close() {
    if (!$this->user->has(new Permission\ProtectPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    parent::close();
  }


  /**
   * @copydoc Post::lock()
   */
  public function lock() {
    if (!$this->user->has(new Permission\ProtectPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    parent::lock();
  }


  /**
   * @copydoc Post::unprotect()
   */
  public function unprotect() {
    if (!$this->user->has(new Permission\UnprotectPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    parent::unprotect();
  }


  /**
   * @copydoc Versionable::submit()
   */
  public function submit() {
    if (!$this->user->has(new Permission\EditPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    parent::submit();
  }


  /**
   * @copydoc Versionable::approve()
   */
  public function approve() {
    $permission = new Permission\ApprovePermission($this);

    if (!$this->user->has($permission))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    $this->castVoteByRole($permission->getRole());

    parent::approve();
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
   * @copydoc Post::editAction()
   */
  public function editAction(BaseController $controller) {
    if (!$this->user->has(new Permission\EditPermission($this)))
      return $controller->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    parent::editAction($controller);
  }


  /**
   * @copydoc Post::viewAction()
   */
  public function viewAction(BaseController $controller) {
    if (!$this->user->has(new Permission\ViewPermission($this)))
      return $controller->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    parent::viewAction($controller);
  }

}
