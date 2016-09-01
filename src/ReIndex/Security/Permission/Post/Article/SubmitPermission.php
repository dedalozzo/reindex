<?php

/**
 * @file Article/SubmitPermission.php
 * @brief This file contains the SubmitPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Post\Article;


use ReIndex\Enum\State;


/**
 * @brief Permission to submit the revision to the Peer Review Committee.
 */
class SubmitPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to submit the article's revision to the Peer Review Committee.";
  }


  public function checkForMemberRole() {
    return $this->user->match($this->article->creatorId) &&
           ($this->article->state->is(State::CREATED) or
            $this->article->state->is(State::DRAFT) or
            $this->article->state->is(State::CURRENT));
  }


  public function checkForEditorRole() {
    return $this->article->state->is(State::CURRENT);
  }

}