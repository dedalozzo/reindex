<?php

//! @file router.php
//! @brief Setting up the router.
//! @details
//! @author Filippo F. Fadda


use Phalcon\Mvc\Router;


// Creates a router instance and return it.
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