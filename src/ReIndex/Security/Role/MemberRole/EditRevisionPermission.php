<?php

/**
 * @file MemberRole/EditRevisionPermission.php
 * @brief This file contains the EditRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Enum\State;


/**
 * @brief Permission to edit a post.
 */
class EditRevisionPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Doc::Post $context
   */
  public function __construct($context = NULL) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Permission to edit a post.";
  }


  /**
   * @brief Returns `true` if the user is the creator of the post and the post is unlocked, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->user->match($this->context->creatorId) &&
      !$this->context->isLocked() &&
      ($this->context->state->is(State::CURRENT) or $this->context->state->is(State::DRAFT)))
      return TRUE;
    else
      return FALSE;
  }

}