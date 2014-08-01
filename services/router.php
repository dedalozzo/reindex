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

    // Order is important, don't change it!
    $router->mount(new Route\IndexGroup());
    $router->mount(new Route\LinkGroup());
    $router->mount(new Route\QuestionGroup());
    $router->mount(new Route\ArticleGroup());
    $router->mount(new Route\BookGroup());

    $router->mount(new Route\TagGroup());
    $router->mount(new Route\BadgeGroup());
    $router->mount(new Route\UserGroup());

    $router->mount(new Route\AuthGroup());
    $router->mount(new Route\AjaxGroup());
    $router->mount(new Route\PostGroup());
    $router->mount(new Route\ProfileGroup());
    $router->mount(new Route\FooterGroup());

    return $router;
  }
);