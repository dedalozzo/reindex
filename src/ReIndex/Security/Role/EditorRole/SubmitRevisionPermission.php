<?php

/**
 * @file EditorRole/SubmitRevisionPermission.php
 * @brief This file contains the SubmitRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\EditorRole;


use ReIndex\Security\Role\MemberRole\SubmitRevisionPermission as Superclass;


/**
 * @copydoc MemberRole::SubmitRevisionPermission
 */
class SubmitRevisionPermission extends Superclass {


  public function check() {
    if ($this->context->state->isSubmittedForPeerReview()) return FALSE;

    if ($this->user->match($this->creatorId) && ($this->context->state->isCreated() or $this->context->state->isDraft()))
      return TRUE;
    else
      return FALSE;
  }


}