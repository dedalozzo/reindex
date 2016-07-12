<?php

/**
 * @file MemberRole/MoveRevisionToTrashPermission.php
 * @brief This file contains the MoveRevisionToTrashPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Doc\Versionable;
use ReIndex\Enum\State;


/**
 * @brief Permission to delete the content.
 */
class MoveRevisionToTrashPermission extends AbstractPermission {


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
    return ($this->user->match($this->context->creatorId) && $this->context->state->is(State::DRAFT)) ? TRUE : FALSE;
  }

}