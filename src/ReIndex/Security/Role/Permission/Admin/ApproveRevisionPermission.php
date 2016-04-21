<?php
/**
 * @file MoveRevisionToTrashPermission.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Admin;


use ReIndex\Security\Role\Permission\Moderator\MoveRevisionToTrashPermission as Superclass;


class MoveVersionToTrashPermission extends Superclass {


  /**
   * @brief Returns `true` if the document can be approved, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->user->isModerator() && ($this->isCreated() or $this->isDraft() or $this->isSubmittedForPeerReview()))
      return TRUE;
    else
      return FALSE;
  }

}