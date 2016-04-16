<?php
/**
 * @file ViewPostPermission.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Moderator;


use ReIndex\Security\Role\Permission\Guest\ViewPostPermission as Superclass;


class ViewPostPermission extends Superclass {

  /**
   * @brief Returns `true` if the post can be viewed by the current moderator, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->post->state->isSubmittedForPeerReview() or
        $this->post->state->isReturnedForRevision() or
        $this->post->state->isRejected() or
        $this->post->state->isMovedToTrash())
      return TRUE;
    else
      return FALSE;
  }

}