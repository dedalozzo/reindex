#! /usr/bin/php
<?php

/**
 * @file rei.php
 * @brief The ReIndex Control application.
 * @details
 * @author Filippo F. Fadda
 */


use ReIndex\Console\Console as ReIndexConsole;
use ReIndex\Console\Command;

use Phalcon\Config\Adapter\Ini as IniReader;
use Phalcon\DI\FactoryDefault as DependencyInjector;

use Monolog\Logger;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;


error_reporting(E_ALL & ~E_NOTICE);

try {
  $root = realpath(__DIR__."/../");

  // Initializes the Composer autoloading system. (Note: We don't use the Phalcon loader.)
  require $root."/vendor/autoload.php";

  // Reads the application's configuration.
  $config = new IniReader($root.'/config.ini');

  $log = new Logger('rei');

  // Registers the Monolog error handler to log errors and exceptions.
  ErrorHandler::register($log);

  // Creates a stream handler to log debugging messages.
  $log->pushHandler(new StreamHandler($root.'/'.$config->application->logDir."reindex.log", Logger::DEBUG));

  // The FactoryDefault Dependency Injector automatically registers the right services providing a full stack framework.
  $di = new DependencyInjector();

  // Initializes the services. The order doesn't matter.
  require $root . "/services/config.php";
  require $root . "/services/log.php";
  require $root . "/services/couchdb.php";
  require $root . "/services/redis.php";
  require $root . "/services/markdown.php";
  require $root . "/services/guardian.php";

  // Creates the application object.
  $console = new ReIndexConsole('ReIndex CLI', ReIndex\Version::getNumber());
  //$console->setCatchExceptions(FALSE);

  // Sets the dependency injector component.
  $console->setDI($di);

  $console->add(new Command\AboutCommand());
  $console->add(new Command\CreateCommand());
  $console->add(new Command\InitCommand());
  $console->add(new Command\GrantCommand());
  $console->add(new Command\RevokeCommand());
  $console->add(new Command\CacheCommand());
  $console->add(new Command\InstallCommand());

  $console->run();
}
catch (Exception $e) {
  echo $e;
}