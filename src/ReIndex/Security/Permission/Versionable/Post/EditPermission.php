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
abstract class EditPermission extends AbstractPermission {


  /**
   * @brief Returns `true` if the user is the creator of the post and the post is unlocked, `false` otherwise.
   * @retval bool
   */
  public function checkForMemberRole() {
    if (!$this->post->isLocked() &&
        ($this->post->state->is(State::CURRENT) || ($this->post->state->is(State::DRAFT) && $this->user->match($this->post->creatorId))))
      return TRUE;
    else
      return FALSE;
  }


  public function checkForEditorRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
      return (!$this->post->isLocked() && $this->post->state->is(State::CURRENT)) ? TRUE : FALSE;
  }


  public function checkForReviewerRole() {
    if ($this->checkForEditorRole())
      return TRUE;
    else
      return !$this->post->isLocked() &&
      ($this->post->state->is(State::CURRENT) || $this->post->state->is(State::SUBMITTED))
        ? TRUE : FALSE;
  }


  public function checkForModeratorRole() {
    return $this->post->state->is(State::CURRENT) or $this->post->state->is(State::SUBMITTED);
  }

}