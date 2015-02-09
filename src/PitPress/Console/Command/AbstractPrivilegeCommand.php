<?php

/**
 * @file AbstractPrivilegeCommand.php
 * @brief This file contains the AbstractPrivilegeCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;


/**
 * @brief Ancestor of grant and revokes commands.
 * @nosubgrouping
 */
abstract class AbstractPrivilegeCommand extends AbstractCommand {

  
  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setDescription(sprintf("%s a privilege to a user.", ucfirst($this->getName().'s')));

    $this->addArgument("privilege",
      InputArgument::REQUIRED,
      sprintf("The privilege you intend to %s to the specified user.", $this->getName()));

    $this->addArgument("username",
      InputArgument::REQUIRED,
      "The username.");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $couch = $this->di['couchdb'];

    $privilege = $input->getArgument('privilege');
    $username = $input->getArgument('username');

    // Sets the options.
    $opts = new ViewQueryOpts();
    $opts->setKey($username)->setLimit(1);

    $result = $couch->queryView('users', 'byUsername', NULL, $opts);

    if (!$result->isEmpty()) {
      $user = $couch->getDoc(Couch::STD_DOC_PATH, $result[0]['id']);
      $methodName = $this->getName().$privilege;

      if (method_exists($user, $methodName)) {
        call_user_func([$user, $methodName]);
        $user->save();
      }
      else
        $output->writeln("The specified privilege doesn't exist.");
    }
    else
      $output->writeln("The user `$username` doesn't exist.");

    parent::execute($input, $output);
  }

}