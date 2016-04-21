<?php

/**
 * @file EditingPermission.php
 * @brief This file contains the EditingPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the editor role
namespace ReIndex\Security\Role\EditorRole;


use ReIndex\Security\Role\MemberRole\EditPostPermission as Superclass;


/**
 * @copydoc Member::EditPostPermission
 */
class EditPostPermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
     return (!$this->post->isLocked() && $this->post->state->isCurrent()) ? TRUE : FALSE;
  }

}