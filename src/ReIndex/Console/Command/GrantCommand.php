<?php

/**
 * @file GrantCommand.php
 * @brief This file contains the GrantCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;


use Symfony\Component\Console\Output\OutputInterface;

use ReIndex\Model\Member;
use ReIndex\Security\Guardian;


/**
 * @brief Grants a privilege to a user.
 * @nosubgrouping
 */
class GrantCommand extends AbstractRoleCommand {


  protected function perform($roleName, Member $member, Guardian $guardian, OutputInterface $output) {
    if ($guardian->roleExists($roleName)) {
      $member->roles->grant($guardian->getRole($roleName));
      $member->save();
    }
    else
      $output->writeln("The role `$roleName` doesn't exist.");
  }


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName('grant');
    parent::configure();
  }

}