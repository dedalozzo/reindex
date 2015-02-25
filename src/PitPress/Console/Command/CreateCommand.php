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

use EoC\Couch;
use EoC\Adapter\NativeAdapter;


/**
 * @brief Creates the PitPress database.
 * @nosubgrouping
 */
class CreateCommand extends AbstractCommand {


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("create");
    $this->setDescription("Creates the PitPress database.");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $config = $this->di['config'];

    $couch = new Couch(new NativeAdapter(NativeAdapter::DEFAULT_SERVER, $config->couchdb->user, $config->couchdb->password));

    $couch->createDb($config->couchdb->database);

    parent::execute($input, $output);
  }

}