<?php

/**
 * @file ApproveRevisionPermission.php
 * @brief This file contains the ApproveRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ReviewerRole;


use ReIndex\Security\Role\MemberRole\ApproveRevisionPermission as Superclass;


/**
 * @copydoc MemberRole::ApproveRevisionPermission
 */
class ApproveRevisionPermission extends Superclass {


  public function check() {
    if ($this->context->state->isCreated() or $this->context->state->isDraft() or $this->context->state->isSubmittedForPeerReview())
      return TRUE;
    else
      return FALSE;
  }

}