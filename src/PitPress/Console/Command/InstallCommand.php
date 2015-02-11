<?php

/**
 * @file InstallCommand.php
 * @brief This file contains the InstallCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

use ElephantOnCouch\Couch;
use ElephantOnCouch\Adapter\NativeAdapter;


/**
 * @brief Executes the following commands: create, prepare, import, init.
 * @nosubgrouping
 */
class InstallCommand extends AbstractCommand {


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("install");
    $this->setDescription("Executes the following commands: create, prepare, import all, init all.");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $config = $this->di['config'];

    // Creates database.
    $couch = new Couch(new NativeAdapter(NativeAdapter::DEFAULT_SERVER, $config->couchdb->user, $config->couchdb->password));
    $couch->createDb($config->couchdb->database);

    // Init all.
    $command = $this->getApplication()->find('init');
    $arguments = [
      'command' => 'init',
      'documents' => ['all']
    ];
    $input = new ArrayInput($arguments);
    $command->run($input, $output);

    // Rebuild cache.
    $command = $this->getApplication()->find('cache');
    $arguments = [
      'command' => 'cache',
      'subcommand' => ['rebuild']
    ];
    $input = new ArrayInput($arguments);
    $command->run($input, $output);
  }

}