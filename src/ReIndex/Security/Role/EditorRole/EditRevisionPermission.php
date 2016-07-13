<?php

/**
 * @file EditorRole/EditRevisionPermission.php
 * @brief This file contains the EditRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the editor role
namespace ReIndex\Security\Role\EditorRole;


use ReIndex\Security\Role\MemberRole\EditRevisionPermission as Superclass;


/**
 * @copydoc MemberRole::EditRevisionPermission
 */
class EditRevisionPermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
     return (!$this->context->isLocked() && $this->context->state->is(State::CURRENT)) ? TRUE : FALSE;
  }

}