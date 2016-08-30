<?php

/**
 * @file MemberRole/ViewPostPermission.php
 * @brief This file contains the ViewPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Security\Role\GuestRole\ViewPostPermission as Superclass;


/**
 * @copybrief GuestRole::ViewPostPermission
 * @details A member can only see his own posts, even if they are invisible to the other members.
 */
class ViewPostPermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
      return $this->user->match($this->context->creatorId) ? TRUE : FALSE;
  }

}


/*
 * Guest
 *

public function check() {
  return $this->context->state->is(State::CURRENT) ? TRUE : FALSE;
}


Reviewer

  public function check() {
    if (parent::check())
      return TRUE;
    else
      return $this->context->state->is(State::SUBMITTED) ? TRUE : FALSE;
  }



/*
 * Moderator
 *
 *
 *
  public function check() {
    if (parent::check())
      return TRUE;
    else
      return ($this->context->state->is(State::CURRENT) or
        $this->context->state->is(State::SUBMITTED) or
        $this->context->state->is(State::REJECTED) or
        $this->context->state->is(State::DELETED)) ? TRUE : FALSE;
  }
 */

