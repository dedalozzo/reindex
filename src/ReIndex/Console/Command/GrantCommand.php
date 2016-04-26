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

use Symfony\Component\Console\Output\OutputInterface;


/**
 * @brief Grants a privilege to a user.
 * @nosubgrouping
 */
class GrantCommand extends AbstractRoleCommand {


  protected function perform(IRole $role, Member $member, OutputInterface $output) {
    if (!$member->roles->areSuperiorThan($role, FALSE))
      $member->roles->grant($role);
    else
      $output->writeln('A superior role already exists for the member.');
  }


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName('grant');
    parent::configure();
  }

}