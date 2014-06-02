<?php

/**
 * @file monolog.php
 * @brief Just a trick to return the logger.
 * @details
 * @author Filippo F. Fadda
 */


// Returns a Monolog instance.
$di->setShared('monolog',
  function() use ($monolog) {
    return $monolog;
  }
);