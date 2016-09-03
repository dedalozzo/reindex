<?php

/**
 * @file Question/RejectPermission.php
 * @brief This file contains the RejectPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Question;


use ReIndex\Enum\State;


/**
 * @brief Permission to vote for the rejection of a document's revision.
 */
class RejectPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to vote for the rejection of a question's revision.";
  }


  /**
   * @brief Returns the value for the vote if the document revision can be rejected, `false` otherwise.
   * @retval mixed
   */
  public function checkForMemberRole() {
    if ($this->question->state->is(State::SUBMITTED) &&
      $this->user->match($this->question->creatorId) &&
      !$this->user->match($this->question->editorId))
      return -$this->di['config']->review->creatorVoteValue;
    else
      return FALSE;
  }


  public function checkForReviewerRole() {
    if ($this->question->state->is(State::SUBMITTED) &&
      !$this->user->match($this->question->editorId))
      return -$this->di['config']->review->reviewerVoteValue;
    else
      return FALSE;
  }


  public function checkForModeratorRole() {
    if ($this->question->state->is(State::SUBMITTED))
      return -$this->di['config']->review->moderatorVoteValue;
    else
      return FALSE;
  }


  public function checkForAdminRole() {
    if ($this->question->state->is(State::SUBMITTED))
      return $this->di['config']->review->scoreToRejectRevision;
    else
      return FALSE;
  }

}