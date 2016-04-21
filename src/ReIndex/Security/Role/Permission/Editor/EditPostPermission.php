<?php

/**
 * @file EditingPermission.php
 * @brief This file contains the EditingPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the editor role
namespace ReIndex\Security\Role\Permission\Editor;


use ReIndex\Security\Role\Permission\Member\EditingPermission as Superclass;


class EditingPermission extends Superclass {


  public function getDescription() {
    //! @todo: Implement getDescription() method.
  }


  /**
   * @brief Returns `true` if the user has, at least, the Editor role and the post is unlocked, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if (!$this->post->isLocked() &&
      ($this->post->isCurrent() or $this->post->isDraft()))
      return TRUE;
    else
      return FALSE;
  }

}