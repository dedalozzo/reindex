<?php
/**
 * @file RestoreVersionPermission.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Moderator;


class RestoreVersionPermission {


  public function getDescription() {
    //! @todo: Implement getDescription() method.
  }


  /**
   * @brief Returns `true` if the post can be moved to trash, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->isMovedToTrash() and
      ($this->user->isModerator() && ($this->dustmanId == $this->user->id)) or
      $this->user->isAdmin())
      return TRUE;
    else
      return FALSE;
  }


}