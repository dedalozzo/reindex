<?php

/**
 * @file AdminRole.php
 * @brief This file contains the AdminRole class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


/**
 * @brief A community's administrator.
 * @details An admin is able to administer the entire community. It's a powerful role, granted to a very few community's
 * members by the supervisor in person.
 * @nosubgrouping
 */
class AdminRole extends ModeratorRole {

  
  public function getDescription() {
    return "A community's administrator";
  }
  
}