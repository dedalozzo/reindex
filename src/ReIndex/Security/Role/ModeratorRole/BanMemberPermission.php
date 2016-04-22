<?php

/**
 * @file ModeratorRole/BanMemberPermission.php
 * @brief This file contains the BanMemberPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\AbstractPermission;


/**
 * @brief Permission to ban another community's member.
 */
class BanMemberPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Model::Member $context
   */
  public function __construct($context = NULL) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Permission to ban another community's member.";
  }


  public function check() {
    if ($this->user->isAdmin() && !$this->member->isAdmin() && !$this->user->match($this->member->id))
      return TRUE;
    elseif ($this->user->isModerator() && !$this->isModerator() && !$this->user->match($this->member->id))
      return TRUE;
    else
      return FALSE;
  }
  
}