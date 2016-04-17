<?php

/**
 * @file RevokeCommand.php
 * @brief This file contains the RevokeCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;


use Symfony\Component\Console\Output\OutputInterface;

use ReIndex\Model\Member;
use ReIndex\Security\Guardian;


/**
 * @brief Revokes a privilege to a user.
 * @nosubgrouping
 */
class RevokeCommand extends AbstractRoleCommand {


  protected function perform($roleName, Member $member, Guardian $guardian, OutputInterface $output) {
    if ($member->roles->exists($roleName)) {
      $member->roles->revoke($roleName);
      $member->save();
    }
    else
      $output->writeln("The role `$roleName` doesn't exist for the member `$member->username`.");
  }


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName('revoke');
    parent::configure();
  }

}