<?php

/**
 * @file Post/RejectPermission.php
 * @brief This file contains the RejectPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post;


use ReIndex\Enum\State;


/**
 * @brief Permission to vote for the rejection of a post's revision.
 */
class RejectPermission extends AbstractPermission {


  /**
   * @brief Returns the value for the vote if the document revision can be rejected, `false` otherwise.
   * @retval mixed
   */
  public function checkForMemberRole() {
    if ($this->post->state->is(State::SUBMITTED) &&
      $this->user->match($this->post->creatorId) &&
      !$this->user->match($this->post->editorId))
      return -$this->di['config']->review->creatorVoteValue;
    else
      return FALSE;
  }


  public function checkForReviewerRole() {
    if ($this->post->state->is(State::SUBMITTED) &&
      !$this->user->match($this->post->editorId))
      return -$this->di['config']->review->reviewerVoteValue;
    else
      return FALSE;
  }


  public function checkForModeratorRole() {
    if ($this->post->state->is(State::SUBMITTED))
      return -$this->di['config']->review->moderatorVoteValue;
    else
      return FALSE;
  }


  public function checkForAdminRole() {
    if ($this->post->state->is(State::SUBMITTED))
      return $this->di['config']->review->scoreToRejectRevision;
    else
      return FALSE;
  }

}