<?php

//! @file CommitCommand.php
//! @brief This file contains the CommitCommand class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ElephantOnCouch\Helper\TimeHelper;


//! @brief Makes sure all uncommited database changes are written and synchronized to the disk.
//! @nosubgrouping
class CommitCommand extends AbstractCommand {


  //! @brief Configures the command.
  protected function configure() {
    $this->setName("commit");
    $this->setDescription("Makes sure all uncommited database changes are written and synchronized to the disk.");
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {
    $couch = $this->_di['couchdb'];

    echo "File Opened Since: ".TimeHelper::since($couch->ensureFullCommit()).PHP_EOL;
  }

}