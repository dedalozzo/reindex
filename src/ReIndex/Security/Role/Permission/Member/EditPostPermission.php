<?php
/**
 * @file EditPostPermission.php
 * @brief This file contains the EditPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Member;


use ReIndex\Security\Role\Permission\AbstractPostPermission;


class EditPostPermission extends AbstractPostPermission {


  /**
   * @brief Returns `true` if the user is the creator of the post and the post is unlocked, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->user->match($this->post->creatorId) &&
      !$this->post->isLocked() &&
      ($this->post->isCurrent() or $this->post->isDraft()))
      return TRUE;
    else
      return FALSE;
  }

}