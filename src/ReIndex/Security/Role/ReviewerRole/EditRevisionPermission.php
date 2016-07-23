<?php

/**
 * @file ReviewerRole/EditRevisionPermission.php
 * @brief This file contains the EditRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ReviewerRole;


use ReIndex\Security\Role\EditorRole\EditRevisionPermission as Superclass;


/**
 * @copydoc EditorRole::EditRevisionPermission
 */
class EditRevisionPermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
     return !$this->context->isLocked() && ($this->context->state->is(State::CURRENT) || $this->context->state->is(State::SUBMITTED)) ? TRUE : FALSE;
  }

}