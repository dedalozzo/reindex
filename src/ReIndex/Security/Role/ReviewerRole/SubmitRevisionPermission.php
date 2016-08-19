<?php

/**
 * @file ReviewerRole/SubmitRevisionPermission.php
 * @brief This file contains the SubmitRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ReviewerRole;


use ReIndex\Security\Role\EditorRole\SubmitRevisionPermission as Superclass;
use ReIndex\Enum\State;


/**
 * @copydoc MemberRole::SubmitRevisionPermission
 */
class SubmitRevisionPermission extends Superclass {


  public function check() {
    return $this->context->state->is(State::CURRENT) or $this->context->state->is(State::SUBMITTED);
  }

}