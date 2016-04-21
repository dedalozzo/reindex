<?php
/**
 * @file ImpersonatePermission.php
 * @brief This file contains the ImpersonatePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\AdminRole;


use ReIndex\Security\Role\MemberRole\ImpersonatePermission as Superclass;


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