<?php

/**
 * @file ModeratorRole/MoveRevisionToTrashPermission.php
 * @brief This file contains the MoveRevisionToTrashPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\MemberRole\MoveRevisionToTrashPermission as Superclass;


/**
 * @copybrief MemberRole::MoveRevisionToTrashPermission
 * @details In addition a moderator can delete any post, with the exception of the protected ones.
 */
class MoveRevisionToTrashPermission extends Superclass {


  public function check() {
    if ($this->context->state->isMovedToTrash())
      return FALSE;
    else
      return !$this->context->isProtected() ? TRUE : FALSE;
  }

}