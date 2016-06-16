<?php

/**
 * @file RevokeCommand.php
 * @brief This file contains the RevokeCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command\Role;


use ReIndex\Model\Member;
use ReIndex\Security\Role\IRole;

use Symfony\Component\Console\Output\OutputInterface;


/**
 * @brief Revokes a privilege to a user.
 * @nosubgrouping
 */
class RevokeCommand extends AbstractRoleCommand {


  protected function perform(IRole $role, Member $member, OutputInterface $output) {
    if ($member->roles->exists($role))
      $member->roles->revoke($role);
    else
      $output->writeln('There is not such role associated to the member.');
  }


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName('revoke');
    parent::configure();
  }

}