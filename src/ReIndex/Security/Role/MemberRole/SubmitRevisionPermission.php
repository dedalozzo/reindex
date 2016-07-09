<?php

/**
 * @file MemberRole/SubmitRevisionPermission.php
 * @brief This file contains the SubmitRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Doc\Versionable;
use ReIndex\Enum\State;


/**
 * @brief Permission to submit the revision to the Peer Review Committee.
 */
class SubmitRevisionPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * param[in] Doc::Versionable $context
   */
  public function __construct(Versionable $context) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Permission to submit the document's revision to the Peer Review Committee.";
  }


  public function check() {
    return $this->context->state->is(State::CREATED) or $this->context->state->is(State::DRAFT);
  }

}