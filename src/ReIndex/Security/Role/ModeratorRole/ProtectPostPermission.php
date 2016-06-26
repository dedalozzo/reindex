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
use ReIndex\Doc\Post;


/**
 * @brief Permission to close or lock a post.
 * @details A moderator can protect only the current revision of a post, just in case it doesn't have any active
 * protection.
 * @nosubgrouping
 */
class ProtectPostPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Doc::Post $context
   */
  public function __construct(Post $context = NULL) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Permission to close or lock a post.";
  }


  public function check() {
    return (!$this->context->isProtected() && $this->context->state->isCurrent()) ? TRUE : FALSE;
  }

}