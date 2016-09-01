<?php

/**
 * @file Article/EditPermission.php
 * @brief This file contains the EditPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Security\Role\Permission\Post\Article\AbstractPermission;
use ReIndex\Enum\State;


/**
 * @brief Permission to edit an article.
 */
class EditPermission extends AbstractPermission {
  

  public function getDescription() {
    return "Permission to edit an article.";
  }


  /**
   * @brief Returns `true` if the user is the creator of the post and the post is unlocked, `false` otherwise.
   * @retval bool
   */
  public function checkForMemberRole() {
    if (!$this->article->isLocked() &&
        ($this->article->state->is(State::CURRENT) or 
         ($this->article->state->is(State::DRAFT) && $this->user->match($this->article->creatorId))))
      return TRUE;
    else
      return FALSE;
  }


  public function checkForEditorRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
     return (!$this->article->isLocked() && $this->article->state->is(State::CURRENT)) ? TRUE : FALSE;
  }


  public function checkForReviewerRole() {
    if ($this->checkForEditorRole())
      return TRUE;
    else
      return !$this->article->isLocked() &&
      ($this->article->state->is(State::CURRENT) || $this->article->state->is(State::SUBMITTED))
        ? TRUE : FALSE;
  }


  public function checkForModeratorRole() {
    return $this->article->state->is(State::CURRENT) or $this->article->state->is(State::SUBMITTED);
  }

}