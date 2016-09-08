<?php

/**
 * @file Post/EditPermission.php
 * @brief This file contains the EditPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post;


use ReIndex\Enum\State;


/**
 * @brief Permission to edit a post.
 */
class EditPermission extends AbstractPermission {


  /**
   * @brief A member can edit any current (and unlocked) post within his own drafts.
   * @retval bool
   */
  public function checkForMemberRole() {
    if (!$this->post->isLocked() &&
        ($this->post->state->is(State::CURRENT) || ($this->post->state->is(State::DRAFT) && $this->user->match($this->post->creatorId))))
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief A reviewer can even edit submitted revisions.
   * @retval bool
   */
  public function checkForReviewerRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
      return !$this->post->isLocked() &&
      ($this->post->state->is(State::CURRENT) || $this->post->state->is(State::SUBMITTED))
        ? TRUE : FALSE;
  }


  /**
   * @brief A moderator doesn't care if a post has been locked.
   * @retval bool
   */
  public function checkForModeratorRole() {
    return $this->post->state->is(State::CURRENT) or $this->post->state->is(State::SUBMITTED);
  }

}