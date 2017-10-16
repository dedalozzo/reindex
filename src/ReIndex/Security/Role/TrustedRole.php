<?php

/**
 * @file TrustedRole.php
 * @brief This file contains the TrustedRole class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


use Daikengo\Role\MemberRole;


/**
 * @brief A trusted member.
 * @details It's a role granted by the system (or manually) to every member has shown to be trustworthy. A trusted
 * member is able to perform operations that are not allowed to a simple member.
 * @nosubgrouping
 */
class TrustedRole extends MemberRole {


  public function getDescription() {
    return "A trusted community's member.";
  }

}