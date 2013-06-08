<?php

//! @file services.php
//! @brief Starts all the services.
//! @details
//! @author Filippo F. Fadda


use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\View;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use ElephantOnCouch\ElephantOnCouch;


// The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework.
$di = new FactoryDefault();


// Setting up the router.
$di->setShared('router',
  function () {
    $router = new Router();

    //$router->setDefaultController("index");
    //$router->setDefaultAction("recents");

    $router->mount(new PitPress\Route\IndexGroup());
    $router->mount(new PitPress\Route\LinksGroup());
    $router->mount(new PitPress\Route\ForumGroup());
    $router->mount(new PitPress\Route\BlogGroup());
    $router->mount(new PitPress\Route\UsersGroup());
    $router->mount(new PitPress\Route\TagsGroup());
    $router->mount(new PitPress\Route\BadgesGroup());

    return $router;
  }
);


// The URL component is used to generate all kind of urls in the application.
$di->setShared('url',
  function() use ($config) {
	  $url = new UrlResolver();
	  $url->setBaseUri($config->application->baseUri);
	  return $url;
  }
);


// Registers Volt as a service.
$di->setShared('volt',
  function($view, $di) use ($config) {
    $volt = new Volt($view, $di);

    $volt->setOptions(
      [
        'compiledPath' => __DIR__.$config->application->cacheDir.'volt/',
        'compiledExtension' => '.compiled',
        'compiledSeparator' => '_'
      ]
    );

    return $volt;
  }
);


// Setting up the view component.
$di->setShared('view',
  function() use ($config) {
    $view = new View();

    $view->setViewsDir(__DIR__.$config->application->viewsDir);

    $view->registerEngines(['.volt' => 'volt']);

    return $view;
  }
);


// Starts the session the first time some component request the session service.
$di->setShared('session',
  function() {
    $session = new SessionAdapter();
    $session->start();
    return $session;
  }
);


// Creates an instance of ElephantOnCouch client.
$di->setShared('couchdb',
  function() use ($config) {
    $couch = new ElephantOnCouch(ElephantOnCouch::DEFAULT_SERVER, $config->couchdb->user, $config->couchdb->password);

    $couch->useCurl();

    $couch->selectDb($config->couchdb->database);

    return $couch;
  }
);