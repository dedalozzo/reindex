<?php

/**
 * @file Post/ViewPermission.php
 * @brief This file contains the ViewPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post;


use ReIndex\Enum\State;


/**
 * @brief Permission to display a post.
 * @details A member can only see his own posts, even if they are invisible to the other members.
 */
class ViewPermission extends AbstractPermission  {


  /**
   * @brief Returns `true` if the state is current, `false` otherwise.
   * @retval bool
   */
  public function checkForGuestRole() {
    return $this->post->state->is(State::CURRENT) ? TRUE : FALSE;
  }


  /**
   * @brief In addition to every current post, a member can display his own posts.
   * @retval bool
   */
  public function checkForMemberRole() {
    if ($this->checkForGuestRole())
      return TRUE;
    else
      return $this->user->match($this->post->creatorId) ? TRUE : FALSE;
  }


  /**
   * @brief A reviewer can see any submitted posts.
   * @retval bool
   */
  public function checkForReviewerRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
      return $this->post->state->is(State::SUBMITTED) ? TRUE : FALSE;
  }


  /**
   * @brief A moderator can display current, submitted, rejected and even deleted posts.
   * @retval bool
   */
  public function checkForModeratorRole() {
    return ($this->post->state->is(State::CURRENT) or
      $this->post->state->is(State::SUBMITTED) or
      $this->post->state->is(State::REJECTED) or
      $this->post->state->is(State::DELETED)) ? TRUE : FALSE;
  }

}