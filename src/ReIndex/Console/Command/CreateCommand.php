<?php

/*
 * @file CreateCommand.php
 * @brief This file contains the CreateCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use EoC\Couch;
use EoC\Adapter\SocketAdapter;


/**
 * @brief Creates the ReIndex databases.
 * @nosubgrouping
 */
final class CreateCommand extends AbstractCommand {


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("create");
    $this->setDescription("Creates the ReIndex databases");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $cf = $this->di['config']['couchdb'];
    $prefix = $this->di['config']['application']['dbPrefix'];

    $couch = new Couch(new SocketAdapter($cf['host'].":".$cf['port'], $cf['user'], $cf['password']));
    $couch->setDbPrefix($prefix);

    $databases = $this->di['init'];
    foreach ($databases as $name => $value) {
      $couch->createDb($name);
      printf('%s%s... done'.PHP_EOL, $prefix, $name);
    }

    $redis = $this->di['redis'];
    $redis->flushAll();
  }

}