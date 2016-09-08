<?php

/**
 * @file Post/ApprovePermission.php
 * @brief This file contains the ApprovePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post;


use ReIndex\Enum\State;


/**
 * @brief Permission to vote for the approval of a post's revision.
 */
abstract class ApprovePermission extends AbstractPermission {


  /**
   * @brief A member can approve any modifications on his own posts.
   * @retval bool
   */
  public function checkForMemberRole() {
    return ($this->post->state->is(State::SUBMITTED) && $this->user->match($this->post->creatorId)) ? TRUE : FALSE;
  }


  /**
   * @brief An editor can approve his own revisions.
   * @retval bool
   */
  public function checkForEditorRole() {
    return $this->post->state->is(State::SUBMITTED) && $this->user->match($this->post->editorId) ? TRUE : FALSE;
  }


  /**
   * @brief A reviewer can approve any modifications.
   * @retval bool
   */
  public function checkForReviewerRole() {
    return $this->post->state->is(State::SUBMITTED) ? TRUE : FALSE;
  }


  /**
   * @brief A moderator can approve any modifications.
   * @attention This method is an alias of `checkForReviewRole`. We need to establish the role of the current member,
   * since in a peer review the vote of a moderator is more important that the one of a reviewer.
   * @retval bool
   */
  public function checkForModeratorRole() {
    return $this->checkForReviewerRole();
  }

}