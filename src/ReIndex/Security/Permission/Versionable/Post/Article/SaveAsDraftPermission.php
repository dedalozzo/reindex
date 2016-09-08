<?php

/**
 * @file Article/SaveAsDraftPermission.php
 * @brief This file contains the SaveAsDraftPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Article;

use ReIndex\Enum\State;


/**
 * @brief Permission to save an article as draft.
 */
class SaveAsDraftPermission extends AbstractPermission  {


  public function getDescription() {
    return "Permission to save an article as draft.";
  }


  public function checkForMemberRole() {
    return $this->user->match($this->article->creatorId) &&
           ($this->article->state->is(State::CREATED) or $this->article->state->is(State::DRAFT))
           ? TRUE : FALSE;
  }

}