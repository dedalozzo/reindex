<?php

/**
 * @file Revision/ViewPermission.php
 * @brief This file contains the ViewPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Revision;


use ReIndex\Enum\State;


/**
 * @brief Permission to display a revision.
 * @details A member can only see his own revisions, even if they are invisible to the other members.
 */
class ViewPermission extends AbstractPermission  {


  /**
   * @brief Returns `true` if the state is current, `false` otherwise.
   * @retval bool
   */
  public function checkForGuestRole() {
    return $this->revision->state->is(State::CURRENT) ? TRUE : FALSE;
  }


  /**
   * @brief In addition to every current revision, a member can display his own revisions.
   * @retval bool
   */
  public function checkForMemberRole() {
    if ($this->checkForGuestRole())
      return TRUE;
    else
      return $this->user->match($this->revision->creatorId) ? TRUE : FALSE;
  }


  /**
   * @brief A reviewer can see any submitted revisions.
   * @retval bool
   */
  public function checkForReviewerRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
      return $this->revision->state->is(State::SUBMITTED) ? TRUE : FALSE;
  }


  /**
   * @brief A moderator can display current, submitted, rejected and even deleted revisions.
   * @retval bool
   */
  public function checkForModeratorRole() {
    return ($this->revision->state->is(State::CURRENT) or
      $this->revision->state->is(State::SUBMITTED) or
      $this->revision->state->is(State::REJECTED) or
      $this->revision->state->is(State::DELETED)) ? TRUE : FALSE;
  }

}