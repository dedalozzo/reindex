<?php

/**
 * @file Article/ApprovePermission.php
 * @brief This file contains the ApprovePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Article;


use ReIndex\Security\Permission\Post\ApprovePermission as Superclass;


/**
 * @brief Permission to vote for the approval of a article's revision.
 */
class ApprovePermission extends Superclass {


  public function getDescription() {
    return "Approves the article revision.";
  }


  /**
   * @brief A member can approve modifications of other than himself on his own posts.
   * @retval bool
   */
  public function checkForMemberRole() {
    if ($this->post->state->is(State::SUBMITTED) &&
      $this->user->match($this->post->creatorId) &&
      !$this->user->match($this->post->editorId)
    )
      return TRUE;
    else
      return FALSE;
  }

}