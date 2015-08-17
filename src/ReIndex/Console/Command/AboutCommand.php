<?php

/**
 * @file AboutCommand.php
 * @brief This file contains the AboutCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Phalcon\Version as PhalconVersion;
use ReIndex\Version as ReIndexVersion;


/**
 * @brief Displays information about ReIndex, like version, database, etc.
 * @nosubgrouping
 */
class AboutCommand extends AbstractCommand {


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("about");
    $this->setDescription("Displays information about ReIndex, like version, database, etc.");
  }


  /**
   * @brief Executes the command.
   * @param[in] InputInterface $input The input interface
   * @param[in] OutputInterface $output The output interface
   * @retval string Information about CouchDB's client, server and the ReIndex database.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $couch = $this->di['couchdb'];
    $redis = $this->di['redis'];

    echo ucfirst($this->di['config']->application->domainName).PHP_EOL;
    echo PHP_EOL;

    echo "[libraries]".PHP_EOL;
    echo "ReIndex ".ReIndexVersion::getNumber().PHP_EOL;
    echo "PHP ".phpversion().PHP_EOL;
    echo "Phalcon ".PhalconVersion::get().PHP_EOL;
    echo $couch->getClientInfo();
    echo PHP_EOL;

    echo "[couchdb]".PHP_EOL;
    echo $couch->getServerInfo();
    echo PHP_EOL;
    echo $couch->getDbInfo();
    echo PHP_EOL;

    echo "[redis]".PHP_EOL;
    $redisInfo = $redis->info();
    echo "Redis ".$redisInfo['redis_version'].PHP_EOL;
    echo PHP_EOL;
    echo "Arch. Bits: ".$redisInfo['arch_bits'].PHP_EOL;
    echo "Uptime (seconds): ".$redisInfo['uptime_in_seconds'].PHP_EOL;
    echo "Uptime (days): ".$redisInfo['uptime_in_days'].PHP_EOL;
    echo "Connected Client: ".$redisInfo['connected_clients'].PHP_EOL;
    echo "Connected Slaves: ".$redisInfo['connected_slaves'].PHP_EOL;
    echo "Used Memory: ".$redisInfo['used_memory'].PHP_EOL;
    echo "Total Connections Received: ".$redisInfo['total_connections_received'].PHP_EOL;
    echo "Total Commands Processed: ".$redisInfo['total_commands_processed'].PHP_EOL;
    echo "Role: ".$redisInfo['role'].PHP_EOL;
    echo PHP_EOL;

    parent::execute($input, $output);
  }

}