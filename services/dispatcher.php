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
    $dispatcher = new Dispatcher();
    return $dispatcher;
  }
);