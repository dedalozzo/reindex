<?php

/**
 * @file ReviewerRole/RejectRevisionPermission.php
 * @brief This file contains the RejectRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ReviewerRole;


use ReIndex\Security\Role\MemberRole\RejectRevisionPermission as Superclass;


/**
 * @copydoc MemberRole::RejectRevisionPermission
 */
class RejectRevisionPermission extends Superclass {


  /**
   * @brief Returns the value for the vote if the document revision can be rejected, `false` otherwise.
   * @retval mixed
   */
  public function check() {
    if ($this->context->state->is(State::SUBMITTED) &&
        !$this->user->match($this->context->editorId))
      return -$this->di['config']->review->reviewerVoteValue;
    else
      return FALSE;
  }

}