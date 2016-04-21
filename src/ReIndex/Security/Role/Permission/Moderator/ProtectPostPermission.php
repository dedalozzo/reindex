<?php
/**
 * @file ProtectPostPermission.php
 * @brief This file contains the ProtectPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the moderator role
namespace ReIndex\Security\Role\Permission\Moderator;


use ReIndex\Security\Role\Permission\AbstractPermission;


class ProtectPostPermission extends AbstractPermission {


  public function getDescription() {
    //! @todo: Implement getDescription() method.
  }


  /**
   * @brief Returns `true` if the protection can be removed from the post, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if (!$this->post->isProtected()) return FALSE;

    if ($this->user->match($this->post->protectorId) &&
      ($this->post->isCurrent() or $this->post->isDraft()))
      return TRUE;
    else
      return FALSE;
  }

}