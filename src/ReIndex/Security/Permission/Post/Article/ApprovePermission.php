<?php

/**
 * @file Article/ApprovePermission.php
 * @brief This file contains the ApprovePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Article;


use ReIndex\Enum\State;


/**
 * @brief Permission to vote for the approval of a article's revision.
 */
class ApprovePermission extends AbstractPermission {


  public function getDescription() {
    return "Approves the article revision.";
  }


  /**
   * @brief Returns `true` if the document can be approved, `false` otherwise.
   * @retval bool
   */
  public function checkForMemberRole() {
    if ($this->article->state->is(State::SUBMITTED) &&
      $this->user->match($this->article->creatorId) &&
      !$this->user->match($this->article->editorId)
    )
      return $this->di['config']->review->creatorVoteValue;
    else
      return FALSE;
  }


  public function checkForReviewerRole() {
    if ($this->article->state->is(State::SUBMITTED) &&
      !$this->user->match($this->article->editorId)
    )
      return $this->di['config']->review->reviewerVoteValue;
    else
      return FALSE;
  }


  public function checkForModeratorRole() {
    if ($this->article->state->is(State::SUBMITTED))
      return $this->di['config']->review->moderatorVoteValue;
    else
      return FALSE;
  }


  public function checkForAdminRole() {
    if ($this->article->state->is(State::SUBMITTED))
      return $this->di['config']->review->scoreToApproveRevision;
    else
      return FALSE;
  }

}