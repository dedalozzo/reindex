<?php

/**
 * @file MemberRole/ApproveRevisionPermission.php
 * @brief This file contains the ApproveRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Doc\Versionable;
use ReIndex\Enum\State;


/**
 * @brief Permission to vote for the approval of a document's revision.
 */
class ApproveRevisionPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Doc::Versionable $context.
   */
  public function __construct(Versionable $context = NULL) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Approves the document revision.";
  }


  /**
   * @brief Returns `true` if the document can be approved, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->context->state->is(State::SUBMITTED) &&
        $this->user->match($this->context->creatorId) &&
        !$this->user->match($this->context->editorId))
      return $this->di['config']->review->creatorVoteValue;
    else
      return FALSE;
  }

}


/*
 * Reviewer
 */

/*
   public function check() {
    if ($this->context->state->is(State::SUBMITTED) &&
        !$this->user->match($this->context->editorId))
      return $this->di['config']->review->reviewerVoteValue;
    else
      return FALSE;
  }

Moderator

  public function check() {
    if ($this->context->state->is(State::SUBMITTED))
      return $this->di['config']->review->moderatorVoteValue;
    else
      return FALSE;
  }


Admin

  public function check() {
    if ($this->context->state->is(State::SUBMITTED))
      return $this->di['config']->review->scoreToApproveRevision;
    else
      return FALSE;
  }


 */