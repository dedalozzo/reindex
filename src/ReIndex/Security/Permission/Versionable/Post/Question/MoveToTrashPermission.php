<?php

/**
 * @file Question/MoveToTrashPermission.php
 * @brief This file contains the MoveToTrashPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post\Question;


use ReIndex\Doc\Comment;
use ReIndex\Enum\State;
use ReIndex\Doc\Update;


/**
 * @brief Permission to delete the content.
 */
class MoveToTrashPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to delete the question.";
  }


  public function checkForMemberRole() {
    if (!$this->user->match($this->question->creatorId))
      return FALSE;

    if ($this->question->state->is(State::DRAFT))
      return TRUE;

    // Special case for updates and comments.
    if ($this->question->state->is(State::CURRENT) && ($this->question instanceof Update or $this->question instanceof Comment))
      return TRUE;

    return FALSE;
  }


  public function checkForModeratorRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
      return $this->question->state->is(State::CURRENT) ? TRUE : FALSE;
  }

}