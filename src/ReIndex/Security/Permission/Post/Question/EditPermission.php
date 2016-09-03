<?php

/**
 * @file Question/EditPermission.php
 * @brief This file contains the EditPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Question;


use ReIndex\Enum\State;


/**
 * @brief Permission to edit an question.
 */
class EditPermission extends AbstractPermission {
  

  public function getDescription() {
    return "Permission to edit an question.";
  }


  /**
   * @brief Returns `true` if the user is the creator of the post and the post is unlocked, `false` otherwise.
   * @retval bool
   */
  public function checkForMemberRole() {
    if (!$this->question->isLocked() &&
        ($this->question->state->is(State::CURRENT) or
         ($this->question->state->is(State::DRAFT) && $this->user->match($this->question->creatorId))))
      return TRUE;
    else
      return FALSE;
  }


  public function checkForEditorRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
     return (!$this->question->isLocked() && $this->question->state->is(State::CURRENT)) ? TRUE : FALSE;
  }


  public function checkForReviewerRole() {
    if ($this->checkForEditorRole())
      return TRUE;
    else
      return !$this->question->isLocked() &&
      ($this->question->state->is(State::CURRENT) || $this->question->state->is(State::SUBMITTED))
        ? TRUE : FALSE;
  }


  public function checkForModeratorRole() {
    return $this->question->state->is(State::CURRENT) or $this->question->state->is(State::SUBMITTED);
  }

}