<?php

/**
 * @file ReviewerRole/ViewPostPermission.php
 * @brief This file contains the ViewPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the reviewer role
namespace ReIndex\Security\Role\ReviewerRole;


use ReIndex\Security\Role\MemberRole\ViewPostPermission as Superclass;


class ViewPostPermission extends Superclass {


  /**
   * @brief A reviewer can see every content has been submitted for peer review.
   * @retval bool
   */
  public function check() {
    if (parent::check())
      return TRUE;
    else
      return $this->context->state->isSubmittedForPeerReview() ? TRUE : FALSE;
  }

}