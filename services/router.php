<?php

/**
 * @file router.php
 * @brief Setting up the router.
 * @details
 * @author Filippo F. Fadda
 */


use Phalcon\Mvc\Router;

use ReIndex\Route;


// Creates a router instance and return it.
$di->setShared('router',
  function () {
    $router = new Router(FALSE);

    $router->notFound(
      [
        'namespace' => 'ReIndex\Controller',
        "controller" => "error",
        "action" => "show404"
      ]
    );

    // Order is important, don't change it!
    $router->mount(new Route\IndexGroup());
    $router->mount(new Route\UpdateGroup());
    $router->mount(new Route\QuestionGroup());
    $router->mount(new Route\ArticleGroup());

    $router->mount(new Route\TagGroup());
    $router->mount(new Route\MemberGroup());

    $router->mount(new Route\AuthGroup());
    $router->mount(new Route\ApiGroup());
    $router->mount(new Route\AjaxGroup());
    $router->mount(new Route\PostGroup());
    $router->mount(new Route\ProfileGroup());
    $router->mount(new Route\FooterGroup());
    $router->mount(new Route\RssGroup());

    return $router;
  }
);