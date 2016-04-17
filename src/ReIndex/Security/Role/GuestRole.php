<?php

/**
 * @file GuestRole.php
 * @brief This file contains the GuestRole class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


/**
 * @brief A guest is an anonymous user.
 * @attention This role cannot be granted to anyone, but instead the security system associates this role to every guest
 * of the site.
 * @nosubgrouping
 */
class GuestRole extends AbstractRole {


  public function getDescription() {
    return "This role is granted to all the anonymous users.";
  }

}