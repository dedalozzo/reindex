<?php

/**
 * @file guardian.php
 * @brief Loads the guardian.
 * @details
 * @author Filippo F. Fadda
 */


use ReIndex\Security;
use ReIndex\Security\Role;


// Creates an instance of Guardian and return it.
$di->setShared('guardian',
  function() use ($config, $di) {
    $guardian = new Security\Guardian($config, $di);
  
    return $guardian;
  }
);