<?php
/**
 * @file MoveToTrashVersionPermission.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Editor;


class MoveToTrashVersionPermission {


  public function getDescription() {
    //! @todo: Implement getDescription() method.
  }


  /**
   * @brief Returns `true` if the document can be moved to trash, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->isMovedToTrash()) return FALSE;

    if (($this->user->isModerator() && $this->isCurrent()) or ($this->user->match($this->creatorId) && $this->isDraft()))
      return TRUE;
    else
      return FALSE;
  }

}