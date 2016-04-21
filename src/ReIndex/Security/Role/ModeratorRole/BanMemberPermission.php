<?php
/**
 * @file BanMemberPermission.php
 * @brief This file contains the BanMemberPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Model\Member;


class BanMemberPermission extends AbstractPermission {
  
  protected $member;


  public function __construct(Member $member = NULL) {
    parent::__construct();
    $this->member = $member;
  }


  public function getDescription() {
    return "Permission to ban another community's member.";
  }


  /**
   * @brief Returns `true` if the user logged in is allowed to ban the current user, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->user->isAdmin() && !$this->member->isAdmin() && !$this->user->match($this->member->id))
      return TRUE;
    elseif ($this->user->isModerator() && !$this->isModerator() && !$this->user->match($this->member->id))
      return TRUE;
    else
      return FALSE;
  }
  
}