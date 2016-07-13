<?php

/**
 * @file ReviewerRole/EditPostPermission.php
 * @brief This file contains the EditingPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the editor role
namespace ReIndex\Security\Role\ReviewerRole;


use ReIndex\Security\Role\EditorRole\EditPostPermission as Superclass;


/**
 * @copydoc ReviewerRole::EditPostPermission
 */
class EditPostPermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
     return !$this->context->isLocked() && ($this->context->state->is(State::CURRENT) || $this->context->state->is(State::SUBMITTED)) ? TRUE : FALSE;
  }

}