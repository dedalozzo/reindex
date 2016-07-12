<?php

/**
 * @file ModeratorRole/ApproveRevisionPermission.php
 * @brief This file contains the ApproveRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\ReviewerRole\ApproveRevisionPermission as Superclass;
use ReIndex\Enum\State;


/**
 * @copydoc ReviewerRole::ApproveRevisionPermission
 */
class ApproveRevisionPermission extends Superclass {


  public function check() {
    if ($this->context->state->is(State::SUBMITTED))
      return $this->di['config']->review->moderatorVoteValue;
    else
      return FALSE;
  }

}