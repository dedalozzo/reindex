<?php

/**
 * @file ModeratorRole/ViewPostPermission.php
 * @brief This file contains the ViewPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\MemberRole\ViewPostPermission as Superclass;
use ReIndex\Enum\State;


/**
 * @copybrief MemberRole::ViewPostPermission
 * @details A moderator (or a superior role) can see every post even when invisible to the other members.
 */
class ViewPostPermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
      return ($this->context->state->is(State::CURRENT) or
        $this->context->state->is(State::SUBMITTED) or
        $this->context->state->is(State::REJECTED) or
        $this->context->state->is(State::DELETED)) ? TRUE : FALSE;
  }

}