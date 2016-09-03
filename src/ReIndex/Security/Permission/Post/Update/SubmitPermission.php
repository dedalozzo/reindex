<?php

/**
 * @file Update/SubmitPermission.php
 * @brief This file contains the SubmitPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Update;


use ReIndex\Enum\State;


/**
 * @brief Permission to submit the revision to the Peer Review Committee.
 */
class SubmitPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to submit the update's revision to the Peer Review Committee.";
  }


  public function checkForMemberRole() {
    return $this->user->match($this->update->creatorId) &&
           ($this->update->state->is(State::CREATED) or
            $this->update->state->is(State::DRAFT) or
            $this->update->state->is(State::CURRENT));
  }


  public function checkForEditorRole() {
    return $this->update->state->is(State::CURRENT);
  }

}