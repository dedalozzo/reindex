<?php

/**
 * @file config.php
 * @brief Just a trick to return the configuration.
 * @details
 * @author Filippo F. Fadda
 */


// Returns the $config object.
$di->setShared('config',
  function() use ($config) {
    return $config;
  }
);