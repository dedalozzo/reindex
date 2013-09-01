#! /usr/bin/php
<?php

//! @file pit.php
//! @brief The PitPress Console application.
//! @details
//! @author Filippo F. Fadda


use PitPress\Console\Console as PitPressConsole;
use PitPress\Console\Command;

use Phalcon\Config\Adapter\Ini as IniReader;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\DI\FactoryDefault as DependencyInjector;

use ElephantOnCouch\Couch;


$start = microtime(true);

try {
  $root = realpath(__DIR__."/../");

  // Initializes the Composer autoloading system. (Note: We don't use the Phalcon loader.)
  require $root."/vendor/autoload.php";

  // Reads the application's configuration.
  $config = new IniReader($root.'/config.ini');

  $logger = new FileAdapter($root."/log/pit.log");
  //$logger->begin();

  // The FactoryDefault Dependency Injector automatically registers the right services providing a full stack framework.
  $di = new DependencyInjector();

  // Initializes the services. The order doesn't matter.
  require $root."/services/config.php";
  require $root."/services/logger.php";
  require $root."/services/couchdb.php";
  require $root."/services/redis.php";
  require $root."/services/mysql.php";
  require $root."/services/markdown.php";

  //Couch::useCurl();

  // Creates the application object.
  $console = new PitPressConsole('PitPress Console', '0.1.0');
  $console->setCatchExceptions(FALSE);

  // Sets the dependency injector component.
  $console->setDI($di);

  $console->add(new Command\AboutCommand());
  $console->add(new Command\CleanupCommand());
  $console->add(new Command\CommitCommand());
  $console->add(new Command\CompactCommand());
  $console->add(new Command\CreateCommand());
  $console->add(new Command\DeleteCommand());
  $console->add(new Command\PrepareCommand());
  $console->add(new Command\RestoreCommand());
  $console->add(new Command\ImportCommand());
  $console->add(new Command\InitCommand());
  $console->add(new Command\InstallCommand());
  $console->add(new Command\QueryCommand());
  $console->add(new Command\StatusCommand());
  $console->add(new Command\GenerateCommand());

  $console->run();
}
catch (Exception $e) {
  echo $e;
}
finally {
  //$logger->commit();
}