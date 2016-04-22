<?php

/**
 * @file ViewPostPermission.php
 * @brief This file contains the ViewPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Security\Role\GuestRole\ViewPostPermission as Superclass;


/**
 * @copydoc Guest::ViewPostPermission
 */
class ViewPostPermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
      return $this->user->match($this->context->creatorId) ? TRUE : FALSE;
  }

}