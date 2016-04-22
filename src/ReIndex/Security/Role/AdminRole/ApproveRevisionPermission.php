<?php

/**
 * @file AdminRole/ApproveRevisionPermission.php
 * @brief This file contains the ApproveRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\AdminRole;


use ReIndex\Security\Role\ModeratorRole\ApproveRevisionPermission as Superclass;


/**
 * @copydoc ModeratorRole::ApproveRevisionPermission
 */
class MoveVersionToTrashPermission extends Superclass {


  public function check() {
    if ($this->user->isModerator() &&
      ($this->context->state->isCreated() or $this->context->state->isDraft() or $this->context->state->isSubmittedForPeerReview()))
      return TRUE;
    else
      return FALSE;
  }

}