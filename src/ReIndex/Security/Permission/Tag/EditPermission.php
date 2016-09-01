<?php

/**
 * @file Tag/EditPermission.php
 * @brief This file contains the EditPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Tag;


use ReIndex\Enum\State;


/**
 * @brief Permission to edit a tag.
 */
class EditPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to edit a tag.";
  }


  /**
   * @brief Returns `true` if the user is the creator of the post and the post is unlocked, `false` otherwise.
   * @retval bool
   */
  public function checkForMemberRole() {
    if ($this->user->match($this->tag->creatorId) &&
       ($this->tag->state->is(State::CURRENT) or $this->tag->state->is(State::DRAFT)))
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief Returns `true` if `checkForMemberRole()` or tag is current, `false` otherwise.
   * @retval bool
   */
  public function checkForEditorRole() {
    if ($this->checkForReviewerRole())
      return TRUE;
    else
      return $this->tag->state->is(State::CURRENT) ? TRUE : FALSE;
  }


  /**
   * @brief Returns `true` if `checkForMemberRole()` or tag is current or submitted, `false` otherwise.
   * @retval bool
   */
  public function checkForReviewerRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
      return ($this->tag->state->is(State::CURRENT) || $this->tag->state->is(State::SUBMITTED)) ? TRUE : FALSE;
  }

}