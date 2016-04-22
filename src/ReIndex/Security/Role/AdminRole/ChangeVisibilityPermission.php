<?php

/**
 * @file ChangeVisibilityPermission
 * @brief This file contains the ChangeVisibilityPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\AdminRole;


use ReIndex\Security\Role\AbstractPermission;


/**
 * @brief Permission to change the post visibility.
 */
class ChangeVisibilityPermission extends AbstractPermission {


  public function getDescription() {
    return "Member can hide the post.";
  }


  public function check() {
    return ($this->context->state->isCurrent() or $this->context->state->isDraft()) ? TRUE : FALSE;
  }

}