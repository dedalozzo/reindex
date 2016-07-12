<?php

/**
 * @file ModeratorRole/MoveRevisionToTrashPermission.php
 * @brief This file contains the MoveRevisionToTrashPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\MemberRole\MoveRevisionToTrashPermission as Superclass;
use ReIndex\Enum\State;


/**
 * @copybrief MemberRole::MoveRevisionToTrashPermission
 * @details In addition a moderator can delete any post, with the exception of the protected ones.
 */
class MoveRevisionToTrashPermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
      return $this->context->state->is(State::CURRENT) && !$this->context->isProtected() ? TRUE : FALSE;
  }

}