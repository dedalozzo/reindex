<?php

/**
 * @file Post/SaveAsDraftPermission.php
 * @brief This file contains the SaveAsDraftPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post;

use ReIndex\Enum\State;


/**
 * @brief Permission to save an article as draft.
 */
class SaveAsDraftPermission extends AbstractPermission  {


  public function checkForMemberRole() {
    return $this->user->match($this->post->creatorId) &&
           ($this->post->state->is(State::CREATED) or $this->post->state->is(State::DRAFT))
           ? TRUE : FALSE;
  }

}