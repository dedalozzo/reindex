<?php
/**
 * @file ViewPostPermission.php
 * @brief This file contains the ViewPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the reviewer role
namespace ReIndex\Security\Role\ReviewerRole;


use ReIndex\Security\Role\MemberRole\ViewPostPermission as Superclass;


class ViewPostPermission extends Superclass {

  /**
   * @brief Returns `true` if the post can be viewed by the current moderator, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if (parent::check())
      return TRUE;
    else
      return $this->post->state->isSubmittedForPeerReview() ? TRUE : FALSE;
  }

}