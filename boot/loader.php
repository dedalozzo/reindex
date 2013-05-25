<?php

//! @file loader.php
//! @brief Load classes automatically based on some conventions.
//! @details
//! @author Filippo F. Fadda


// Creates the loader.
$loader = new \Phalcon\Loader();

// Registers a set of directories taken from the configuration file.
$loader->registerDirs(
  [
    __DIR__.$config->application->controllersDir,
    __DIR__.$config->application->pluginsDir,
    __DIR__.$config->application->libraryDir,
    __DIR__.$config->application->modelsDir
  ]
);

$loader->register();