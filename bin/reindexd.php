#! /usr/bin/php
<?php

/**
 * @file reindexd.php
 * @brief The ReIndex Daemon.
 * @details
 * @author Filippo F. Fadda
 */


use Phalcon\Config\Adapter\Ini as IniReader;
use Phalcon\Di\FactoryDefault as DependencyInjector;

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

  $log = new Logger('reindexd');

  // Registers the Monolog error handler to log errors and exceptions.
  ErrorHandler::register($log);

  // Creates a stream handler to log debugging messages.
  $log->pushHandler(new StreamHandler($root.'/'.$config->application->logDir."reindexd.log", Logger::DEBUG));

  // The FactoryDefault Dependency Injector automatically registers the right services providing a full stack framework.
  $di = new DependencyInjector();

  // Initializes the services. The order doesn't matter.
  require $root . "/services/config.php";
  require $root . "/services/log.php";
  require $root . "/services/taskqueue.php";
  require $root . "/services/couchdb.php";
  require $root . "/services/redis.php";
  require $root . "/services/markdown.php";
  require $root . "/services/guardian.php";

  $taskQueue = $di['taskqueue'];

  // We finally save the book.
  $taskQueue->perform();
}
catch (Exception $e) {
  echo $e;
}