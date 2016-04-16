<?php
/**
 * @file BanMemberPermission.php
 * @brief This file contains the BanMemberPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Admin;


use ReIndex\Security\Role\Permission\Moderator\BanMemberPermission as Superclass;


class BanMemberPermission extends Superclass {


  public function getDescription() {
    //! @todo: Implement getDescription() method.
  }


  /**
   * @brief Returns `true` if the user logged in is allowed to ban the current user, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->user->isAdmin() && !$this->member->isAdmin() && !$this->user->match($this->member->id))
      return TRUE;
    elseif ($this->user->isModerator() && !$this->isModerator() && !$this->user->match($this->member->id))
      return TRUE;
    else
      return FALSE;
  }
  
}