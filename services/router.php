<?php

/**
 * @file router.php
 * @brief Setting up the router.
 * @details
 * @author Filippo F. Fadda
 */


use Phalcon\Mvc\Router;

use PitPress\Route;


// Creates a router instance and return it.
$di->setShared('router',
  function () {
    $router = new Router(FALSE);

    $router->notFound(
      [
        'namespace' => 'PitPress\Controller',
        "controller" => "error",
        "action" => "show404"
      ]
    );

    $router->mount(new Route\IndexGroup());
    $router->mount(new Route\PostGroup());
    $router->mount(new Route\TagsGroup());
    $router->mount(new Route\BadgesGroup());
    $router->mount(new Route\UsersGroup());
    $router->mount(new Route\AuthGroup());
    $router->mount(new Route\ProfileGroup());
    $router->mount(new Route\AjaxGroup());

    return $router;
  }
);