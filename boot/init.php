<?php

//! @file init.php
//! @brief Initializes the application.
//! @details
//! @author Filippo F. Fadda


use Phalcon\DI\FactoryDefault;


$start = microtime(true);

try {
  // Initializes the Composer autoloading system. (Note: We don't use the Phalcon loader.)
  require __DIR__."/../vendor/autoload.php";


  // Reads the application's configuration.
  $config = new Phalcon\Config\Adapter\Ini(__DIR__.'/../config.ini');

  // The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework.
  $di = new FactoryDefault();

  // Start the services.
  require __DIR__."/../boot/services/couchdb.php";
  require __DIR__."/../boot/services/mysql.php";
  require __DIR__."/../boot/services/router.php";
  require __DIR__."/../boot/services/view.php";
  require __DIR__."/../boot/services/volt.php";
  require __DIR__."/../boot/services/url.php";
  require __DIR__."/../boot/services/session.php";

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



  // Creates the application object.
  /*$application = new Phalcon\Mvc\Application();

  // Sets the dependency injector component.
  $application->setDI($di);

  // Handles the request.
  echo $application->handle()->getContent(); */
}
catch (Exception $e) {
  echo $e->getMessage();
}

$stop = microtime(true);
$time = round($stop - $start, 3);