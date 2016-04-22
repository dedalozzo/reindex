<?php

/**
 * @file ApproveRevisionPermission.php
 * @brief This file contains the ApproveRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Model\Versionable;


/**
 * @brief Permission to approve a revision.
 */
class ApproveRevisionPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Model::Versionable $context.
   */
  public function __construct(Versionable $context) {
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
    if ($this->user->isModerator() && ($this->context->state->isCreated() or $this->context->state->isDraft() or $this->context->state->isSubmittedForPeerReview()))
      return TRUE;
    else
      return FALSE;
  }

}