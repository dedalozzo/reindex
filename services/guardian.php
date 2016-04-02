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
    
    $guardian->loadRole(new Role\SupervisorRole());
    $guardian->loadRole(new Role\AdminRole());
    $guardian->loadRole(new Role\ModeratorRole());
    $guardian->loadRole(new Role\ReviewerRole());
    $guardian->loadRole(new Role\EditorRole());

    return $guardian;
  }
);