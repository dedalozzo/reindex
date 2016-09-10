<?php

/**
 * @file Article/ApprovePermission.php
 * @brief This file contains the ApprovePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Revision\Post\Article;


use ReIndex\Security\Permission\Revision\ApprovePermission as Superclass;


/**
 * @brief Permission to vote for the approval of an article's revision.
 */
class ApprovePermission extends Superclass {


  /**
   * @brief A member can approve modifications of other than himself on his own revisions.
   * @retval bool
   */
  public function checkForMemberRole() {
    if ($this->revision->state->is(State::SUBMITTED) &&
      $this->user->match($this->revision->creatorId) &&
      !$this->user->match($this->revision->editorId)
    )
      return TRUE;
    else
      return FALSE;
  }

}