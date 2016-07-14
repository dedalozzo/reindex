<?php

/**
 * @file ModeratorRole/MoveToTrashPermission.php
 * @brief This file contains the MoveToTrashPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\MemberRole\MoveToTrashPermission as Superclass;
use ReIndex\Enum\State;


/**
 * @copybrief MemberRole::MoveToTrashPermission
 * @details In addition a moderator can delete any post, with the exception of the protected ones.
 */
class MoveToTrashPermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
      return $this->context->state->is(State::CURRENT) ? TRUE : FALSE;
  }

}