<?php

/**
 * @file RevertToVersionPermission.php
 * @brief This file contains the RevertToVersionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Moderator;


use ReIndex\Security\Role\Permission\AbstractPermission;


class RevertToVersionPermission extends AbstractPermission {


  public function getDescription() {
    //! @todo: Implement getDescription() method.
  }


  /**
   * @brief Returns `true` if the document can be reverted to another version, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->user->isModerator())
      return TRUE;
    else
      return FALSE;
  }

}