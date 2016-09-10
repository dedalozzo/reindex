<?php

/**
 * @file Post/ProtectPermission.php
 * @brief This file contains the ProtectPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Revision\Post;


use ReIndex\Enum\State;


/**
 * @brief Permission to close or lock a post.
 */
class ProtectPermission extends AbstractPermission {


  /**
   * @brief A moderator can protect only the current revision of a post, just in case it doesn't have any active
   * protection.
   * @retval bool
   */
  public function checkForModeratorRole() {
    return (!$this->post->isProtected() && $this->post->state->is(State::CURRENT)) ? TRUE : FALSE;
  }

}