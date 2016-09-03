<?php

/**
 * @file Update/ViewPermission.php
 * @brief This file contains the ViewPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Update;


use ReIndex\Enum\State;


/**
 * @brief Permission to display an update.
 * @details A member can only see his own updates, even if they are invisible to the other members.
 */
class ViewPermission extends AbstractPermission  {


  public function getDescription() {
    return "Permission to read an update.";
  }


  public function checkForGuestRole() {
    return $this->update->state->is(State::CURRENT) ? TRUE : FALSE;
  }


  public function checkForMemberRole() {
    if ($this->checkForGuestRole())
      return TRUE;
    else
      return $this->user->match($this->update->creatorId) ? TRUE : FALSE;
  }


  public function checkForReviewerRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
      return $this->update->state->is(State::SUBMITTED) ? TRUE : FALSE;
  }


  public function checkForModeratorRole() {
    return ($this->update->state->is(State::CURRENT) or
      $this->update->state->is(State::SUBMITTED) or
      $this->update->state->is(State::REJECTED) or
      $this->update->state->is(State::DELETED)) ? TRUE : FALSE;
  }

}