<?php

/**
 * @file ReviewerRole/EditPostPermission.php
 * @brief This file contains the EditPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ReviewerRole;


use ReIndex\Security\Role\EditorRole\EditPostPermission as Superclass;
use ReIndex\Enum\State;


/**
 * @copydoc EditorRole::EditPostPermission
 */
class EditRevisionPermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
     return !$this->context->isLocked() && ($this->context->state->is(State::CURRENT) || $this->context->state->is(State::SUBMITTED)) ? TRUE : FALSE;
  }

}