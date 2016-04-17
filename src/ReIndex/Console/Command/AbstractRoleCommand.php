<?php

/**
 * @file AbstractRoleCommand.php
 * @brief This file contains the AbstractRoleCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use ReIndex\Model\Member;
use ReIndex\Security\Guardian;


/**
 * @brief Ancestor of grant and revokes commands.
 * @nosubgrouping
 */
abstract class AbstractRoleCommand extends AbstractCommand {


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setDescription(sprintf("%s a role to a user.", ucfirst($this->getName().'s')));

    $this->addArgument("role",
      InputArgument::REQUIRED,
      sprintf("The role's name you intend to %s to the specified user.", $this->getName()));

    $this->addArgument("username",
      InputArgument::REQUIRED,
      "The username.");
  }


  abstract protected function perform($roleName, Member $member, Guardian $guadian, OutputInterface $output);


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $guardian = $this->di['guardian'];
    $couch = $this->di['couchdb'];

    $roleName = $input->getArgument('role');
    $username = $input->getArgument('username');

    // Sets the options.
    $opts = new ViewQueryOpts();
    $opts->setKey($username)->setLimit(1);

    $result = $couch->queryView('members', 'byUsername', NULL, $opts);

    if (!$result->isEmpty()) {
      $member = $couch->getDoc(Couch::STD_DOC_PATH, $result[0]['id']);

      $this->perform($roleName, $member, $guardian, $output);
    }
    else
      $output->writeln("A member with the username `$username` doesn't exist.");

    parent::execute($input, $output);
  }

}