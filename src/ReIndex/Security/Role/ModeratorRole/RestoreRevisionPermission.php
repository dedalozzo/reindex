<?php
/**
 * @file RestoreRevisionPermission.php
 * @brief This file contains the RestoreRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


class RestoreRevisionPermission {


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