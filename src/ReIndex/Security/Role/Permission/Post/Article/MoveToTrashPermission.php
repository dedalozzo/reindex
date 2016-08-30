<?php

/**
 * @file MemberRole/MoveToTrashPermission.php
 * @brief This file contains the MoveToTrashPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Doc\Comment;
use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Doc\Versionable;
use ReIndex\Enum\State;
use ReIndex\Doc\Update;


/**
 * @brief Permission to delete the content.
 */
class MoveToTrashPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * param[in] Doc::Versionable $context
   */
  public function __construct(Versionable $context = NULL) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Permission to delete the content.";
  }


  public function check() {
    if (!$this->user->match($this->context->creatorId))
      return FALSE;

    if ($this->context->state->is(State::DRAFT))
      return TRUE;

    // Special case for updates and comments.
    if ($this->context->state->is(State::CURRENT) && ($this->context instanceof Update or $this->context instanceof Comment))
      return TRUE;

    return FALSE;
  }

}


/*
 * Moderator
 */


/*
public function check() {
  if (parent::check())
    return TRUE;
  else
    return $this->context->state->is(State::CURRENT) ? TRUE : FALSE;
}
*/
