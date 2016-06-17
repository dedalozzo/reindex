<?php

/**
 * @file AbstractRoleCommand.php
 * @brief This file contains the AbstractRoleCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command\Role;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ReIndex\Console\Command\AbstractCommand;
use ReIndex\Model\Member;
use ReIndex\Security\Role\IRole;
use ReIndex\Factory\UserFactory;


/**
 * @brief Ancestor of grant and revokes commands.
 * @nosubgrouping
 */
abstract class AbstractRoleCommand extends AbstractCommand {


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setDescription(sprintf("%s a role to a user", ucfirst($this->getName().'s')));

    $this->addArgument("role",
      InputArgument::REQUIRED,
      sprintf("The role's name you intend to %s to the specified user", $this->getName()));

    $this->addArgument("username",
      InputArgument::REQUIRED,
      "The username");
  }


  /**
   * @brief Performs the operation.
   */
  abstract protected function perform(IRole $role, Member $member, OutputInterface $output);


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $guardian = $this->di['guardian'];

    $roleName = $input->getArgument('role');
    $username = $input->getArgument('username');

    $member = UserFactory::fromUsername($username);

    if ($member->isMember()) {
      if ($guardian->roleExists($roleName)) {
        $role = $guardian->getRole($roleName);
        $this->perform($role, $member, $output);
        $member->save();
      }
      else
        $output->writeln("The role `$roleName` doesn't exist.");
    }
    else
      $output->writeln("A member with the username `$username` doesn't exist.");
  }

}