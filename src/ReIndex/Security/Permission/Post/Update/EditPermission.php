<?php

/**
 * @file Update/EditPermission.php
 * @brief This file contains the EditPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Update;


use ReIndex\Enum\State;


/**
 * @brief Permission to edit an update.
 */
class EditPermission extends AbstractPermission {
  

  public function getDescription() {
    return "Permission to edit an update.";
  }


  /**
   * @brief Returns `true` if the user is the creator of the post and the post is unlocked, `false` otherwise.
   * @retval bool
   */
  public function checkForMemberRole() {
    if (!$this->update->isLocked() &&
        ($this->update->state->is(State::CURRENT) or
         ($this->update->state->is(State::DRAFT) && $this->user->match($this->update->creatorId))))
      return TRUE;
    else
      return FALSE;
  }


  public function checkForEditorRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
     return (!$this->update->isLocked() && $this->update->state->is(State::CURRENT)) ? TRUE : FALSE;
  }


  public function checkForReviewerRole() {
    if ($this->checkForEditorRole())
      return TRUE;
    else
      return !$this->update->isLocked() &&
      ($this->update->state->is(State::CURRENT) || $this->update->state->is(State::SUBMITTED))
        ? TRUE : FALSE;
  }


  public function checkForModeratorRole() {
    return $this->update->state->is(State::CURRENT) or $this->update->state->is(State::SUBMITTED);
  }

}