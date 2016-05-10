<?php

/**
 * @file AdminRole/ImpersonatePermission.php
 * @brief This file contains the ImpersonatePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\AdminRole;


use ReIndex\Security\Role\MemberRole\ImpersonatePermission as Superclass;
use ReIndex\Security\Role\AdminRole;


/**
 * @copydoc MemberRole::ImpersonatePermission
 */
class ImpersonatePermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
      return !$this->context->roles->areSuperiorThan(new AdminRole());
  }

}