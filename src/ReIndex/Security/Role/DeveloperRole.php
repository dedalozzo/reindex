<?php

/**
 * @file DeveloperRole.php
 * @brief This file contains the DeveloperRole class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


/**
 * @brief A platform developer.
 * @details A platform developer is able to use some special debugging tools.
 * @nosubgrouping
 */
class DeveloperRole extends AbstractRole {


  public function getDescription() {
    return "A platform developer.";
  }

}