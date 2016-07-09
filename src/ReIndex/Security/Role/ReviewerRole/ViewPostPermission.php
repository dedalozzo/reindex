<?php

/**
 * @file ReviewerRole/ViewPostPermission.php
 * @brief This file contains the ViewPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the reviewer role
namespace ReIndex\Security\Role\ReviewerRole;


use ReIndex\Security\Role\MemberRole\ViewPostPermission as Superclass;
use ReIndex\Enum\State;


/**
 * @copybrief GuestRole::ViewPostPermission
 * @details A reviewer can see every content has been submitted for peer review.
 */
class ViewPostPermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
      return $this->context->state->is(State::SUBMITTED) ? TRUE : FALSE;
  }

}