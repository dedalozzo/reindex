<?php

/**
 * @file ReviewerRole/RejectRevisionPermission.php
 * @brief This file contains the RejectRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ReviewerRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Doc\Versionable;
use ReIndex\Enum\State;


/**
 * @brief Permission to vote for the rejection of a document's revision.
 */
class RejectRevisionPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Doc::Versionable $context
   */
  public function __construct(Versionable $context) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Permission to vote for the rejection of a document's revision.";
  }


  /**
   * @brief Returns the value for the vote if the document revision can be rejected, `false` otherwise.
   * @retval mixed
   */
  public function check() {
    if ($this->context->state->is(State::SUBMITTED))
      return -$this->di['config']->review->reviewerVoteValue;
    else
      return FALSE;
  }

}