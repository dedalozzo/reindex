<?php

//! @file bootstrap.php
//! @brief Initializes the application.
//! @details
//! @author Filippo F. Fadda


use Phalcon\Config\Adapter\Ini as IniReader;
use Phalcon\DI\FactoryDefault as DependencyInjector;
use Phalcon\Mvc\Application as Application;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

use Monolog\Logger;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;


$start = microtime(true);

setlocale(LC_TIME, 'it_IT');

$root = __DIR__;

// Initializes the Composer autoloading system. (Note: We don't use the Phalcon loader.)
require $root."/vendor/autoload.php";

$whoops = new Run();
$whoops->pushHandler(new PrettyPageHandler());
$whoops->register();

// Reads the application's configuration.
$config = new IniReader($root.'/config.ini');

//$logger = new FileAdapter($root."/log/pit-press.log");
$monolog = new Logger('pit-press');

// Registers the Monolog error handler to log errors and exceptions.
ErrorHandler::register($monolog);

// Creates a stream handler to log debugging messages.
$streamHandler = new StreamHandler($root.$config->application->logDir."pit-press.log");
$streamHandler->setFormatter(new LineFormatter());
$monolog->pushHandler($streamHandler, Logger::DEBUG);

// The FactoryDefault Dependency Injector automatically registers the right services providing a full stack framework.
$di = new DependencyInjector();

// Initializes the services. The order doesn't matter.
require $root."/services/config.php";
require $root."/services/monolog.php";
require $root."/services/dispatcher.php";
require $root."/services/router.php";
require $root."/services/view.php";
require $root."/services/volt.php";
require $root."/services/session.php";
require $root."/services/couchdb.php";
require $root."/services/redis.php";
require $root."/services/markdown.php";
require $root."/services/crypt.php";
require $root."/services/flash.php";
require $root."/services/guardian.php";
require $root."/services/badgeloader.php";


/*
// USE THE FOLLOWING CODE FOR DEBUG PURPOSE ONLY

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
$application = new Application();

// Sets the dependency injector component.
$application->setDI($di);

// Handles the request.
echo $application->handle()->getContent();


$stop = microtime(true);
$time = round($stop - $start, 3);
//echo $time;