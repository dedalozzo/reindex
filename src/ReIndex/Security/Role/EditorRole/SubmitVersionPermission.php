<?php

/**
 * @file SubmitVersionPermission.php
 * @brief This file contains the SubmitVersionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\EditorRole;


/**
 * @copydoc MemberRole::SubmitVersionPermission
 */
class SubmitVersionPermission {


  public function check() {
    if ($this->context->state->isSubmittedForPeerReview()) return FALSE;

    if ($this->user->match($this->creatorId) && ($this->context->state->isCreated() or $this->context->state->isDraft()))
      return TRUE;
    else
      return FALSE;
  }


}