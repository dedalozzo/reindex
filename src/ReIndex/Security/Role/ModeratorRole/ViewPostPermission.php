<?php

/**
 * @file ModeratorRole/ViewPostPermission.php
 * @brief This file contains the ViewPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\MemberRole\ViewPostPermission as Superclass;


/**
 * @copybrief MemberRole::ViewPostPermission
 * @details A moderator (or a superior role) can see every post even when invisible to the other members.
 */
class ViewPostPermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
      return ($this->context->state->isCurrent() or
        $this->context->state->isSubmittedForPeerReview() or
        $this->context->state->isReturnedForRevision() or
        $this->context->state->isRejected() or
        $this->context->state->isMovedToTrash()) ? TRUE : FALSE;
  }

}