<?php

/**
 * @file Article/ViewPermission.php
 * @brief This file contains the ViewPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Article;


use ReIndex\Enum\State;


/**
 * @brief Permission to display an article.
 * @details A member can only see his own articles, even if they are invisible to the other members.
 */
class ViewPermission extends AbstractPermission  {


  public function getDescription() {
    return "Permission to read an article.";
  }


  public function checkForGuestRole() {
    return $this->article->state->is(State::CURRENT) ? TRUE : FALSE;
  }


  public function checkForMemberRole() {
    if ($this->checkForGuestRole())
      return TRUE;
    else
      return $this->user->match($this->article->creatorId) ? TRUE : FALSE;
  }


  public function checkForReviewerRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
      return $this->article->state->is(State::SUBMITTED) ? TRUE : FALSE;
  }


  public function checkForModeratorRole() {
    return ($this->article->state->is(State::CURRENT) or
      $this->article->state->is(State::SUBMITTED) or
      $this->article->state->is(State::REJECTED) or
      $this->article->state->is(State::DELETED)) ? TRUE : FALSE;
  }

}