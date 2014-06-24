<?php

/**
 * @file AboutCommand.php
 * @brief This file contains the AboutCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * @brief Displays information about PitPress, like version, database, etc.
 * @nosubgrouping
 */
class AboutCommand extends AbstractCommand {


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("about");
    $this->setDescription("Displays information about PitPress, like version, database, etc.");
  }


  /**
   * @brief Executes the command.
   * @param[in] InputInterface $input The input interface
   * @param[in] OutputInterface $output The output interface
   * @return string Information about CouchDB's client, server and the PitPress database.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $couch = $this->di['couchdb'];

    echo "[server]".PHP_EOL.PHP_EOL;
    echo $couch->getServerInfo();
    echo PHP_EOL;
    echo "[client]".PHP_EOL.PHP_EOL;
    echo $couch->getClientInfo();
    echo PHP_EOL;
    echo "[database]".PHP_EOL.PHP_EOL;
    echo $couch->getDbInfo();

    parent::execute($input, $output);
  }

}