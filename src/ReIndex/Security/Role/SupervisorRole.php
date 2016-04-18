<?php

/**
 * @file SupervisorRole.php
 * @brief This file contains the SupervisorRole class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


/**
 * @brief The community's supervisor.
 * @details A supervisor represent the most important role inside the community. A supervisor is above every other
 * member, included the admins.
  * @nosubgrouping
 */
class SupervisorRole extends AdminRole {


  public function getDescription() {
    return "Special role granted to the site owner.";
  }

}