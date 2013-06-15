<?php

//! @file init.php
//! @brief Initializes the application.
//! @details
//! @author Filippo F. Fadda


use Phalcon\DI\FactoryDefault\CLI;
use Phalcon\CLI\Console;


$start = microtime(true);

try {
  // Initializes the Composer autoloading system. (Note: We don't use the Phalcon loader.)
  require __DIR__."/../vendor/autoload.php";

  // Reads the application's configuration.
  $config = new Phalcon\Config\Adapter\Ini(__DIR__.'/../config.ini');

  // The FactoryDefault Dependency Injector automatically registers the right services providing a full stack framework.
  $di = new CLI();

  // Initializes the services. The order doesn't matter.
  require __DIR__."/../boot/services/config.php";
  require __DIR__."/../boot/services/couchdb.php";
  require __DIR__."/../boot/services/mysql.php";


  // Creates the application object.
  $console = new Console();

  // Sets the dependency injector component.
  $console->setDI($di);

  // Handles the request.
  echo $console->handle($_SERVER['argv']);
}
catch (Exception $e) {
  echo $e->getMessage();
}