<?php

/**
 * @file Article/MoveToTrashPermission.php
 * @brief This file contains the MoveToTrashPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post\Article;


use ReIndex\Doc\Comment;
use ReIndex\Enum\State;
use ReIndex\Doc\Update;


/**
 * @brief Permission to delete the content.
 */
class MoveToTrashPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to delete the article.";
  }


  public function checkForMemberRole() {
    if (!$this->user->match($this->article->creatorId))
      return FALSE;

    if ($this->article->state->is(State::DRAFT))
      return TRUE;

    // Special case for updates and comments.
    if ($this->article->state->is(State::CURRENT) && ($this->article instanceof Update or $this->article instanceof Comment))
      return TRUE;

    return FALSE;
  }


  public function checkForModeratorRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
      return $this->article->state->is(State::CURRENT) ? TRUE : FALSE;
  }

}