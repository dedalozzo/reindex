<?php

/**
 * @file ModeratorRole/RejectRevisionPermission.php
 * @brief This file contains the RejectRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\ReviewerRole\RejectRevisionPermission as Superclass;


/**
 * @copydoc ReviewerRole::RejectRevisionPermission
 */
class RejectRevisionPermission extends Superclass {


  /**
   * @brief Returns the value for the vote if the document revision can be rejected, `false` otherwise.
   * @retval mixed
   */
  public function check() {
    if ($this->context->state->is(State::SUBMITTED))
      return -$this->di['config']->review->moderatorVoteValue;
    else
      return FALSE;
  }

}