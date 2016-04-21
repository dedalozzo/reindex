<?php

/**
 * @file ChangeVisibilityPermission
 * @brief This file contains the ChangeVisibilityPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Admin;


use ReIndex\Security\Role\Permission\AbstractPermission;


class ChangeVisibilityPermission extends AbstractPermission {



  public function getDescription() {
    return "Member can hide the post.";
  }


  /**
   * @brief Returns `true` if the user can hide or show the post, `false` otherwise.
   * @retval bool
   */
  public function check() {
    return ($this->post->isCurrent() or $this->post->isDraft()) ? TRUE : FALSE;
  }

}