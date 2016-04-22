<?php

/**
 * @file ModeratorRole/ProtectPostPermission.php
 * @brief This file contains the ProtectPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the moderator role
namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\AbstractPermission;


/**
 * @brief Permission to close or lock a post.
 */
class ProtectPostPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Model::Post $context
   */
  public function __construct($context = NULL) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Permission to close or lock a post.";
  }


  public function check() {
    if (!$this->context->isProtected()) return FALSE;

    if ($this->user->match($this->context->protectorId) &&
      ($this->post->context->isCurrent() or $this->context->state->isDraft()))
      return TRUE;
    else
      return FALSE;
  }

}