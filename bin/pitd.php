#! /usr/bin/php
<?php

//! @file pitd.php
//! @brief The PitPress daemon.
//! @details
//! @author Filippo F. Fadda

use Phalcon\Config\Adapter\Ini as IniReader;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\DI\FactoryDefault\CLI as DependencyInjector;
use Phalcon\CLI\Console;

use ElephantOnCouch\Couch;

try {
  $root = realpath(__DIR__."/../");

  // Initializes the Composer autoloading system. (Note: We don't use the Phalcon loader.)
  require __DIR__."/../vendor/autoload.php";

  // Reads the application's configuration.
  $config = new IniReader($root.'/config.ini');

  $logger = new FileAdapter($root."/log/pitd.log");
  $logger->begin();

  // The FactoryDefault Dependency Injector automatically registers the right services providing a full stack framework.
  $di = new DependencyInjector();

  // Initializes the services. The order doesn't matter.
  require $root."/services/config.php";
  require $root."/services/logger.php";
  require $root."/services/couchdb.php";
  require $root."/services/mysql.php";

  //Couch::useCurl();

  // Creates the application object.
  $console = new Console();

  // Sets the dependency injector component.
  $console->setDI($di);

  // Retrieves the Dispatcher component.
  $dispatcher = $console->getDI()->getShared('dispatcher');

  // Sets the default namespace, where to find tasks.
  $dispatcher->setDefaultNamespace('PitPress\Task');

  @list($process, $task, $action) = $argv;

  if (isset($task)) {
    $dispatcher->setTaskName($task);

    if (isset($action))
      $dispatcher->setActionName($action);
  }

  // Dispatches the request.
  $dispatcher->dispatch();

  //echo "Task Name: ".$dispatcher->getTaskName().PHP_EOL;
  //echo "Action Name: ".$dispatcher->getActionName().PHP_EOL;
}
catch (Exception $e) {
  echo $e;
}
finally {
  $logger->commit();
}