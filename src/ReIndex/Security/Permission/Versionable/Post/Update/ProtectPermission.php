<?php

/**
 * @file Update/ProtectPermission.php
 * @brief This file contains the ProtectPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Posts related permissions
namespace ReIndex\Security\Permission\Versionable\Post\Update;


use ReIndex\Doc\Update;
use ReIndex\Enum\State;


/**
 * @brief Permission to close or lock a post.
 * @details A moderator can protect only the current revision of a post, just in case it doesn't have any active
 * protection.
 * @nosubgrouping
 */
class ProtectPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to close or lock a post.";
  }


  /**
   * @brief Returns `true` if the post can be protected, `false` otherwise.
   * @retval bool
   */
  public function checkForModeratorRole() {
    return (!$this->update->isProtected() && $this->update->state->is(State::CURRENT)) ? TRUE : FALSE;
  }

}