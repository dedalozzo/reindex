<?php

/**
 * @file Article/RejectPermission.php
 * @brief This file contains the RejectPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Post\Article;


use ReIndex\Enum\State;


/**
 * @brief Permission to vote for the rejection of a document's revision.
 */
class RejectPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to vote for the rejection of a article's revision.";
  }


  /**
   * @brief Returns the value for the vote if the document revision can be rejected, `false` otherwise.
   * @retval mixed
   */
  public function checkForMemberRole() {
    if ($this->article->state->is(State::SUBMITTED) &&
      $this->user->match($this->article->creatorId) &&
      !$this->user->match($this->article->editorId))
      return -$this->di['config']->review->creatorVoteValue;
    else
      return FALSE;
  }


  public function checkForReviewerRole() {
    if ($this->article->state->is(State::SUBMITTED) &&
      !$this->user->match($this->article->editorId))
      return -$this->di['config']->review->reviewerVoteValue;
    else
      return FALSE;
  }


  public function checkForModeratorRole() {
    if ($this->article->state->is(State::SUBMITTED))
      return -$this->di['config']->review->moderatorVoteValue;
    else
      return FALSE;
  }


  public function checkForAdminRole() {
    if ($this->article->state->is(State::SUBMITTED))
      return $this->di['config']->review->scoreToRejectRevision;
    else
      return FALSE;
  }

}