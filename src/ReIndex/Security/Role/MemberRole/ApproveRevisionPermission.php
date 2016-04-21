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


class ApproveRevisionPermission extends AbstractPermission {

  public $versionable;


  public function __construct(Versionable $versionable) {
    parent::__construct();
    $this->versionable = $versionable;
  }


  public function getDescription() {
    return "Approves the document revision.";
  }


  /**
   * @brief Returns `true` if the document can be approved, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->user->isModerator() && ($this->isCreated() or $this->isDraft() or $this->isSubmittedForPeerReview()))
      return TRUE;
    else
      return FALSE;
  }

}