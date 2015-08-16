<?php

/**
 * @file change-manager.php
 * @brief Loads the change manager.
 * @details
 * @author Filippo F. Fadda
 */


use ReIndex\Mediator\ChangeManager;


// Creates an instance of the change manager and return it.
$di->setShared('changeManager',
  function() use ($config, $di) {
    $changeManager = new ChangeManager($config, $di);

    return $changeManager;
  }
);