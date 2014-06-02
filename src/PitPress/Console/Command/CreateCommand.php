<?php

/*
 * @file CreateCommand.php
 * @brief This file contains the CreateCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ElephantOnCouch\Couch;
use ElephantOnCouch\Adapter\NativeAdapter;


/**
 * @brief Creates a new PitPress database.
 * @nosubgrouping
 */
class CreateCommand extends AbstractCommand {


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("create");
    $this->setDescription("Creates a new database.");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $config = $this->di['config'];

    $couch = new Couch(new NativeAdapter(NativeAdapter::DEFAULT_SERVER, $config->couchdb->user, $config->couchdb->password));

    $couch->createDb($config->couchdb->database);
  }

}