<?php

/**
 * @file Question/ViewPermission.php
 * @brief This file contains the ViewPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Question;


use ReIndex\Enum\State;


/**
 * @brief Permission to display an question.
 * @details A member can only see his own questions, even if they are invisible to the other members.
 */
class ViewPermission extends AbstractPermission  {


  public function getDescription() {
    return "Permission to read an question.";
  }


  public function checkForGuestRole() {
    return $this->question->state->is(State::CURRENT) ? TRUE : FALSE;
  }


  public function checkForMemberRole() {
    if ($this->checkForGuestRole())
      return TRUE;
    else
      return $this->user->match($this->question->creatorId) ? TRUE : FALSE;
  }


  public function checkForReviewerRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
      return $this->question->state->is(State::SUBMITTED) ? TRUE : FALSE;
  }


  public function checkForModeratorRole() {
    return ($this->question->state->is(State::CURRENT) or
      $this->question->state->is(State::SUBMITTED) or
      $this->question->state->is(State::REJECTED) or
      $this->question->state->is(State::DELETED)) ? TRUE : FALSE;
  }

}