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
  // Initializes the Composer autoloading system. (Note: We don't use the Phalcon loader.)
  require __DIR__."/../vendor/autoload.php";

  // Reads the application's configuration.
  $config = new IniReader(__DIR__.'/../config.ini');

  $logger = new FileAdapter("/tmp/pit.log");

  // The FactoryDefault Dependency Injector automatically registers the right services providing a full stack framework.
  $di = new DependencyInjector();

  // Initializes the services. The order doesn't matter.
  require __DIR__."/../services/config.php";
  require __DIR__."/../services/logger.php";
  require __DIR__."/../services/couchdb.php";
  require __DIR__."/../services/redis.php";
  require __DIR__."/../services/mysql.php";
  require __DIR__."/../services/markdown.php";

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

  $console->run();
}
catch (Exception $e) {
  echo $e;
}