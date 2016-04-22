<?php

/**
 * @file MoveRevisionToTrashPermission.php
 * @brief This file contains the MoveRevisionToTrashPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\ReviewerRole\ApproveRevisionPermission as Superclass;


/**
 * @copydoc ReviewerRole::ApproveRevisionPermission
 */
class ApproveRevisionPermissionPermission extends Superclass {


  public function check() {
    if  ($this->context->state->isCreated() or $this->context->state->isDraft() or $this->context->state->isSubmittedForPeerReview())
      return TRUE;
    else
      return FALSE;
  }

}