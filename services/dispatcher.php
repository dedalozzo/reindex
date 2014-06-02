<?php

/**
 * @file dispatcher.php
 * @brief Creates the dispatcher component.
 * @details
 * @author Filippo F. Fadda
 */


use Phalcon\Mvc\Dispatcher;


// Returns the dispatcher instance.
$di->setShared('dispatcher',
  function() use ($di) {
    $eventsManager = $di->getShared('eventsManager');

    $eventsManager->attach(
      "dispatch:beforeException",
      function($event, $dispatcher, $exception) {
        $code = $exception->getCode();

        if ($code == Dispatcher::EXCEPTION_HANDLER_NOT_FOUND or $code == Dispatcher::EXCEPTION_ACTION_NOT_FOUND) {
          $dispatcher->forward(
            [
              'controller' => 'error',
              'action'     => 'show404'
            ]
          );
        }
      }
    );

    $dispatcher = new Dispatcher();
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
  }
);