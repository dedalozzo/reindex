<?php
/**
 * @file ImpersonateMemberPermission.php
 * @brief This file contains the ImpersonateMemberPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Admin;


use ReIndex\Security\Role\Permission\AbstractPermission;


class ImpersonateMemberPermission extends AbstractPermission {

  /**
   * @brief Returns `true` is the current user can impersonate the specified user, `false` otherwise.
   * @details An admin can impersonate any member, but he can't impersonate another admin. A member (even an admin) can
   * impersonate a guest. No one can impersonate itself and a guest, of course, can't impersonate anyone.
   * @param[in] IUser $user An user instance.
   * @retval bool
   */
  private function check() {
    if ($this->$user->isAdmin() && $user->isMember() && !$user->isAdmin())
      return TRUE;
    elseif ($this->user->isMember() && $user->isGuest())
      return TRUE;
    else
      return FALSE;
  }

}