<?php

/**
 * @file log.php
 * @brief Just a trick to return the logger.
 * @details
 * @author Filippo F. Fadda
 */


// Returns a Monolog instance.
$di->setShared('log',
  function() use ($log) {
    return $log;
  }
);