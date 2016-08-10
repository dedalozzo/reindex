<?php

/**
 * @file init.php
 * @brief Parses the init.yaml file and returns an array with the database configuration.
 * @details
 * @author Filippo F. Fadda
 */


// Returns the $config object.
$di->setShared('init',
  function() use ($root) {
    return yaml_parse_file($root.'/etc/init.yaml');
  }
);