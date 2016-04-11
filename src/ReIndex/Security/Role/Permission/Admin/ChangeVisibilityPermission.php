<?php
/**
 * @file ChangeVisibilityPermission
 * @brief This file contains the ChangeVisibilityPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Admin;


use ReIndex\Security\Role\Permission\AbstractPostPermission;


class ChangeVisibilityPermission extends AbstractPostPermission {
  

  /**
   * @brief Returns `true` if the user can hide or show the post, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->post->isCurrent() or $this->post->isDraft())
      return TRUE;
    else
      return FALSE;
  }

}