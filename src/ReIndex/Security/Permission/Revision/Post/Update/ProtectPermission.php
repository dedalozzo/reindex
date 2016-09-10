<?php

/**
 * @file Update/ProtectPermission.php
 * @brief This file contains the ProtectPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post\Update;


use ReIndex\Security\Permission\Versionable\Post\ProtectPermission as Superclass;
use ReIndex\Enum\State;


/**
 * @brief Permission to close or lock an update.
 */
class ProtectPermission extends Superclass {


  /**
   * @brief A member can protect only the current revision of his own post, just in case it doesn't have any active
   * protection.
   * @retval bool
   */
  public function checkForMemberRole() {
    return (!$this->post->isProtected() &&
      $this->post->state->is(State::CURRENT) &&
      $this->user->match($this->post->creatorId))
      ? TRUE : FALSE;
  }

}