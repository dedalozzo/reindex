<?php
/**
 * @file ImpersonatePermission.php
 * @brief This file contains the ImpersonatePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Admin;


use ReIndex\Security\Role\Permission\Member\ImpersonationPermission as Superclass;
use ReIndex\Security\Role\AdminRole;


/**
 * @copydoc Member::ImpersonatePermission
 */
class ImpersonatePermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
      return !$this->user->roles->isSuperior(new AdminRole());
  }

}