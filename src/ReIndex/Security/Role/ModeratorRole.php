<?php

/**
 * @file ModeratorRole.php
 * @brief This file contains the ModeratorRole class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


/**
 * @brief A community's moderator.
 * @details A moderator is able to perform many special operations. It's a powerful role, granted to a few community's
 * members.
 * @nosubgrouping
 */
class ModeratorRole extends ReviewerRole {


  public function getDescription() {
    return "A community's moderator.";
  }

}