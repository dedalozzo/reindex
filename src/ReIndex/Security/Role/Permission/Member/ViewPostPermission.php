<?php
/**
 * @file ViewPostPermission.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Member;


class ViewPostPermission {

  /**
   * @brief Returns `true` if the post can be viewed by the current user, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->isCurrent()) return TRUE;

    elseif ($this->user->match($this->creatorId)) return TRUE;

    elseif ($this->user->isEditor() && $this->approved()) return TRUE;

    elseif ($this->user->isModerator() &&
      ($this->isSubmittedForPeerReview() or
        $this->isReturnedForRevision() or
        $this->isRejected() or
        $this->isMovedToTrash()))
      return TRUE;
    else
      return FALSE;
  }

}