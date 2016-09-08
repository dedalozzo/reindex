<?php

/**
 * @file Post/MoveToTrashPermission.php
 * @brief This file contains the MoveToTrashPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post;


use ReIndex\Doc\Comment;
use ReIndex\Enum\State;
use ReIndex\Doc\Update;


/**
 * @brief Permission to delete a post.
 */
class MoveToTrashPermission extends AbstractPermission {


  public function checkForMemberRole() {
    if (!$this->user->match($this->post->creatorId))
      return FALSE;

    if ($this->post->state->is(State::DRAFT))
      return TRUE;

    // Special case for updates and comments.
    if ($this->post->state->is(State::CURRENT) && ($this->post instanceof Update or $this->post instanceof Comment))
      return TRUE;

    return FALSE;
  }


  public function checkForModeratorRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
      return $this->post->state->is(State::CURRENT) ? TRUE : FALSE;
  }

}