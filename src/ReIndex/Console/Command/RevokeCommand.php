<?php

/**
 * @file RevokeCommand.php
 * @brief This file contains the RevokeCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;


use ReIndex\Model\Member;
use ReIndex\Security\Role\IRole;


/**
 * @brief Revokes a privilege to a user.
 * @nosubgrouping
 */
class RevokeCommand extends AbstractRoleCommand {


  protected function perform(IRole $role, Member $member) {
    $member->roles->revoke($role);
  }


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName('revoke');
    parent::configure();
  }

}