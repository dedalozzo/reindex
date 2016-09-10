<?php

/**
 * @file Tag/EditPermission.php
 * @brief This file contains the EditPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Revision\Tag;


use ReIndex\Enum\State;


/**
 * @brief Permission to edit the tags' information.
 */
class EditPermission extends AbstractPermission {


  /**
   * @brief A member can edit any current tag.
   * @retval bool
   */
  public function checkForMemberRole() {
    return $this->tag->state->is(State::CURRENT) ? TRUE : FALSE;
  }


  /**
   * @brief A reviewer can even edit submitted revision.
   * @retval bool
   */
  public function checkForReviewerRole() {
    return ($this->checkForMemberRole() || $this->tag->state->is(State::SUBMITTED)) ? TRUE : FALSE;
  }

}