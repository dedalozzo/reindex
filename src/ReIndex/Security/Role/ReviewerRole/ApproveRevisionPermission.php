<?php

/**
 * @file ReviewerRole/ApproveRevisionPermission.php
 * @brief This file contains the ApproveRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ReviewerRole;


use ReIndex\Security\Role\MemberRole\ApproveRevisionPermission as Superclass;
use ReIndex\Enum\State;


/**
 * @copydoc MemberRole::ApproveRevisionPermission
 */
class ApproveRevisionPermission extends Superclass {


  public function check() {
    if (!$this->user->match($this->context->editorId) && $this->context->state->is(State::SUBMITTED))
      return $this->di['config']->review->reviewerVoteValue;
    else
      return FALSE;
  }

}