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