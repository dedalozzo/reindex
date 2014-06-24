<?php

/**
 * @file CleanupCommand.php
 * @brief This file contains the CleanupCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * @brief Removes all outdated view indexes.
 * @nosubgrouping
 */
class CleanupCommand extends AbstractCommand {


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("cleanup");
    $this->setDescription("Removes all outdated view indexes.");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $couch = $this->di['couchdb'];

    $couch->cleanupViews();

    parent::execute($input, $output);
  }

}