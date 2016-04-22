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
 * @copydoc Member::ViewPostPermission
 */
class ViewPostPermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
      return ($this->context->state->isSubmittedForPeerReview() or
        $this->context->state->isReturnedForRevision() or
        $this->context->state->isRejected() or
        $this->context->state->isMovedToTrash()) ? TRUE : FALSE;
  }

}