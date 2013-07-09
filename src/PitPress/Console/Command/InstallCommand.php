<?php

//! @file InstallCommand.php
//! @brief This file contains the InstallCommand class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;


//! @brief Executes the following commands: create, prepare, import, init.
//! @nosubgrouping
class InstallCommand extends AbstractCommand {


  //! @brief Configures the command.
  protected function configure() {
    $this->setName("install");
    $this->setDescription("Executes the following commands: create, prepare, import all, init.");
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {

    // create
    $command = $this->getApplication()->find('create');
    $arguments = [
      'command' => 'create'
    ];
    $input = new ArrayInput($arguments);
    $command->run($input, $output);

    // prepare
    $command = $this->getApplication()->find('prepare');
    $arguments = [
      'command' => 'prepare'
    ];
    $input = new ArrayInput($arguments);
    $command->run($input, $output);

    // import
    $command = $this->getApplication()->find('import');
    $arguments = [
      'command' => 'import',
      'entities' => ['all']
    ];
    $input = new ArrayInput($arguments);
    $command->run($input, $output);

    // init
    $command = $this->getApplication()->find('init');
    $arguments = [
      'command' => 'init'
    ];
    $input = new ArrayInput($arguments);
    $command->run($input, $output);
  }

}