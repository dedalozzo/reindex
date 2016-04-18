<?php

/**
 * @file GrantCommand.php
 * @brief This file contains the GrantCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;


use ReIndex\Model\Member;
use ReIndex\Security\Role\IRole;


/**
 * @brief Grants a privilege to a user.
 * @nosubgrouping
 */
class GrantCommand extends AbstractRoleCommand {


  protected function perform(IRole $role, Member $member) {
    $member->roles->grant($role);
  }


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName('grant');
    parent::configure();
  }

}