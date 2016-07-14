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
    return $this->context->state->is(State::CURRENT);
  }

}