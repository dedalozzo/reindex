<?php

/**
 * @file AdminRole/RejectRevisionPermission.php
 * @brief This file contains the RejectRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\AdminRole;


use ReIndex\Security\Role\ModeratorRole\RejectRevisionPermission as Superclass;


/**
 * @brief Permission to vote for the rejection of a document's revision.
 */
class RejectRevisionPermission extends Superclass {


  public function getDescription() {
    return "Permission to vote for the rejection of a document's revision.";
  }


  /**
   * @brief Returns the value for the vote if the document revision can be rejected, `false` otherwise.
   * @retval mixed
   */
  public function check() {
    if ($this->context->state->is(State::SUBMITTED))
      return $this->di['config']->review->scoreToRejectRevision;
    else
      return FALSE;
  }

}