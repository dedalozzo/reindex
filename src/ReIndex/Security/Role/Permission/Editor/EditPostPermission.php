<?php
/**
 * @file EditPostPermission.php
 * @brief This file contains the EditPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Editor;


use ReIndex\Security\Role\Permission\Member\EditPostPermission as Superclass;


class EditPostPermission extends Superclass {


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