<?php

/**
 * @file Question/ApprovePermission.php
 * @brief This file contains the ApprovePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Question;


use ReIndex\Enum\State;


/**
 * @brief Permission to vote for the approval of a question's revision.
 */
class ApprovePermission extends AbstractPermission {


  public function getDescription() {
    return "Approves the question revision.";
  }


  /**
   * @brief Returns `true` if the document can be approved, `false` otherwise.
   * @retval bool
   */
  public function checkForMemberRole() {
    if ($this->question->state->is(State::SUBMITTED) &&
      $this->user->match($this->question->creatorId) &&
      !$this->user->match($this->question->editorId)
    )
      return $this->di['config']->review->creatorVoteValue;
    else
      return FALSE;
  }


  public function checkForReviewerRole() {
    if ($this->question->state->is(State::SUBMITTED) &&
      !$this->user->match($this->question->editorId)
    )
      return $this->di['config']->review->reviewerVoteValue;
    else
      return FALSE;
  }


  public function checkForModeratorRole() {
    if ($this->question->state->is(State::SUBMITTED))
      return $this->di['config']->review->moderatorVoteValue;
    else
      return FALSE;
  }


  public function checkForAdminRole() {
    if ($this->question->state->is(State::SUBMITTED))
      return $this->di['config']->review->scoreToApproveRevision;
    else
      return FALSE;
  }

}