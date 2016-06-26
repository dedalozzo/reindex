<?php

/**
 * @file ModeratorRole/BanMemberPermission.php
 * @brief This file contains the BanMemberPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Doc\Member;


/**
 * @brief Permission to ban another community's member.
 * @details A moderator (or a member with a superior role) can ban another member, but only if the member has an
 * inferior role. And of course he cannot ban himself.
 */
class BanMemberPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Doc::Member $context
   */
  public function __construct(Member $context = NULL) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Permission to ban another community's member.";
  }


  public function check() {
    if ($this->context->isBanned())
      return FALSE;
    elseif ($this->user->match($this->context->id))
      return FALSE;
    else
      return !$this->context->areSuperiorThan($this->getRole()) ? TRUE : FALSE;
  }
  
}