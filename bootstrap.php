<?php

//! @file bootstrap.php
//! @brief Initializes the application.
//! @details
//! @author Filippo F. Fadda


use Phalcon\Config\Adapter\Ini as IniReader;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\DI\FactoryDefault as DependencyInjector;


$start = microtime(true);

//try {

$debug = new Phalcon\Debug();
$debug->listen();

  $root = __DIR__;

  // Initializes the Composer autoloading system. (Note: We don't use the Phalcon loader.)
  require $root."/vendor/autoload.php";

  // Reads the application's configuration.
  $config = new IniReader($root.'/config.ini');

  $logger = new FileAdapter($root."/log/pit-press.log");
  $logger->begin();

  // The FactoryDefault Dependency Injector automatically registers the right services providing a full stack framework.
  $di = new DependencyInjector();

  // Initializes the services. The order doesn't matter.
  require $root."/services/config.php";
  require $root."/services/logger.php";
  require $root."/services/dispatcher.php";
  require $root."/services/router.php";
  require $root."/services/view.php";
  require $root."/services/volt.php";
  require $root."/services/session.php";
  require $root."/services/couchdb.php";
  require $root."/services/redis.php";
  require $root."/services/mysql.php";
  require $root."/services/markdown.php";
  require $root."/services/crypt.php";


/*
  // Retrieves the Router component.
  $router = $di['router'];

  // Handle the current route.
  $router->handle();

  // Retrieves the Dispatcher component.
  $dispatcher = $di['dispatcher'];

  // Passes the processed router parameters to the dispatcher.
  $dispatcher->setNamespaceName($router->getNamespaceName());
  $dispatcher->setControllerName($router->getControllerName());
  $dispatcher->setActionName($router->getActionName());
  $dispatcher->setParams($router->getParams());

  // Dispatches the request.
  $dispatcher->dispatch();

  // Retrieves the View component.
  $view = $di['view'];

  // Starts the related view.
  $view->start();

  // Renders the related views.
  $view->render(
    $dispatcher->getControllerName(),
    $dispatcher->getActionName(),
    $dispatcher->getParams()
  );

  // Finishes the related view.
  $view->finish();

  // Retrieves the Response component.
  $response = $di['response'];

  // Passes the output of the view to the response.
  $response->setContent($view->getContent());

  // Sends the request headers.
  $response->sendHeaders();

  // Prints the response.
  echo $response->getContent();

*/
  // Creates the application object.
  $application = new Phalcon\Mvc\Application();

  // Sets the dependency injector component.
  $application->setDI($di);

  // Handles the request.
  echo $application->handle()->getContent();
//}
/*catch (Exception $e) {
  echo $e;
}
finally {
  $logger->commit();
}*/

$stop = microtime(true);
$time = round($stop - $start, 3);
//echo $time;