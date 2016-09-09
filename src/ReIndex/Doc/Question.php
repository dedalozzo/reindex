<?php

/**
 * @file Question.php
 * @brief This file contains the Question class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Security\Permission\Versionable\Post\Question as Permission;
use Reindex\Exception;
use ReIndex\Controller\BaseController;


/**
 * @brief A question asked by a member.
 * @nosubgrouping
 */
final class Question extends Post {


  /**
   * @copydoc Versionable::revert()
   */
  public function revert($versionNumber = NULL) {
    if (!$this->user->has(new Permission\RevertPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    parent::revert($versionNumber);
  }

}
