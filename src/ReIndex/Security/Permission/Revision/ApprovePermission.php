<?php

/**
 * @file Revision/ApprovePermission.php
 * @brief This file contains the ApprovePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Revision;


use ReIndex\Enum\State;


/**
 * @brief Permission to vote for the approval of a revision.
 */
class ApprovePermission extends AbstractPermission {


  /**
   * @brief A member can approve any modifications on his own revisions.
   * @retval bool
   */
  public function checkForMemberRole() {
    return $this->revision->state->is(State::SUBMITTED) &&
           $this->user->match($this->revision->creatorId)
      ? TRUE : FALSE;
  }


  /**
   * @brief An editor can approve his own revisions.
   * @retval bool
   */
  public function checkForEditorRole() {
    return $this->revision->state->is(State::SUBMITTED) &&
           $this->user->match($this->revision->editorId)
      ? TRUE : FALSE;
  }

}