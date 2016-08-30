<?php

/**
 * @file BanPermission.php
 * @brief This file contains the BanPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Member;


use ReIndex\Security\Role\Permission\AbstractPermission;
use ReIndex\Doc\Member;


/**
 * @brief Permission to ban another community's member.
 * @details A moderator (or a member with a superior role) can ban another member,
 * but only if the member has an inferior role. And of course he cannot ban himself.
 */
class BanPermission extends AbstractPermission {

  protected $member;


  /**
   * @brief Constructor.
   * @param[in] Doc::Member $member
   */
  public function __construct(Member $member) {
    $this->member = $member;
    parent::__construct();
  }


  public function getDescription() {
    return "Permission to ban another community's member.";
  }


  /**
   * @brief A moderator can ban another member.
   * @return bool
   */
  public function checkForModeratorRole() {
    if ($this->member->isBanned())
      return FALSE;
    elseif ($this->user->match($this->member->id))
      return FALSE;
    else
      return !$this->member->roles->areSuperiorThan($this->getRole()) ? TRUE : FALSE;
  }
  
}