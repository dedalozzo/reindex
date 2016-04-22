<?php

/**
 * @file MemberRole/EditPostPermission.php
 * @brief This file contains the EditPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Security\Role\AbstractPermission;


/**
 * @brief Permission to edit a post.
 */
class EditPostPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Model::Post $context
   */
  public function __construct($context = NULL) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Permission to edit a post.";
  }


  /**
   * @brief Returns `true` if the user is the creator of the post and the post is unlocked, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->user->match($this->context->creatorId) &&
      !$this->context->isLocked() &&
      ($this->context->state->isCurrent() or $this->context->state->isDraft()))
      return TRUE;
    else
      return FALSE;
  }

}