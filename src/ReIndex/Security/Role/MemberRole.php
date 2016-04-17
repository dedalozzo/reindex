<?php

/**
 * @file MemberRole.php
 * @brief This file contains the MemberRole class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


/**
 * @brief A classic community's member.
 * @attention This role is granted to every community's member. You cannot revoke this role unless a superior role has
 * been granted to the member.
 * @nosubgrouping
 */
class MemberRole extends GuestRole {


  public function getDescription() {
    return "This role is granted to every community's member.";
  }

}