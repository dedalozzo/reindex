<?php
/**
 * @file RevertVersionPermission.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Moderator;


class RevertVersionPermission {


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