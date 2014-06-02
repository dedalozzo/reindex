<?php

/**
 * @file CompactCommand.php
 * @brief This file contains the CompactCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ElephantOnCouch\Couch;


/**
 * @brief Starts a compaction for the current selected database.
 * @nosubgrouping
 */
class CompactCommand extends AbstractCommand {


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("compact");
    $this->setDescription("Starts a compaction for the current selected database.");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $couch = $this->di['couchdb'];

    $couch->compactDb();
  }

}