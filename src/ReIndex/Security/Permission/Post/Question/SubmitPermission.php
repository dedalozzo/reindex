<?php

/**
 * @file Question/SubmitPermission.php
 * @brief This file contains the SubmitPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Question;


use ReIndex\Enum\State;


/**
 * @brief Permission to submit the revision to the Peer Review Committee.
 */
class SubmitPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to submit the question's revision to the Peer Review Committee.";
  }


  public function checkForMemberRole() {
    return $this->user->match($this->question->creatorId) &&
           ($this->question->state->is(State::CREATED) or
            $this->question->state->is(State::DRAFT) or
            $this->question->state->is(State::CURRENT));
  }


  public function checkForEditorRole() {
    return $this->question->state->is(State::CURRENT);
  }

}