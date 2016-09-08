<?php

/**
 * @file Update/MoveToTrashPermission.php
 * @brief This file contains the MoveToTrashPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post\Update;


use ReIndex\Doc\Comment;
use ReIndex\Enum\State;
use ReIndex\Doc\Update;


/**
 * @brief Permission to delete the content.
 */
class MoveToTrashPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to delete the update.";
  }


  public function checkForMemberRole() {
    if (!$this->user->match($this->update->creatorId))
      return FALSE;

    if ($this->update->state->is(State::DRAFT))
      return TRUE;

    // Special case for updates and comments.
    if ($this->update->state->is(State::CURRENT) && ($this->update instanceof Update or $this->update instanceof Comment))
      return TRUE;

    return FALSE;
  }


  public function checkForModeratorRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
      return $this->update->state->is(State::CURRENT) ? TRUE : FALSE;
  }

}