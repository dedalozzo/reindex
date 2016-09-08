<?php

/**
 * @file Tag/EditPermission.php
 * @brief This file contains the EditPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Tag;


use ReIndex\Enum\State;


/**
 * @brief Permission to edit the tags' information.
 */
class EditPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to edit a tag.";
  }


  /**
   * @brief A member can edit any current tag.
   * @retval bool
   */
  public function checkForMemberRole() {
    return $this->tag->state->is(State::CURRENT) ? TRUE : FALSE;
  }


  /**
   * @brief A reviewer can edit current tags and submitted revisions.
   * @retval bool
   */
  public function checkForReviewerRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else
      return ($this->tag->state->is(State::CURRENT) || $this->tag->state->is(State::SUBMITTED)) ? TRUE : FALSE;
  }

}