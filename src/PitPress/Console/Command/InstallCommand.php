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
    $this->setDescription("Executes the following commands: `create`, `init all`, `cache rebuild`.");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    // Creates database.
    $command = $this->getApplication()->find('create');
    $arguments = [
      'command' => 'create'
    ];
    $input = new ArrayInput($arguments);
    $command->run($input, $output);

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