<?php

/**
 * @file guardian.php
 * @brief Loads the guardian.
 * @details
 * @author Filippo F. Fadda
 */


use PitPress\Security;


// Creates an instance of Guardian and return it.
$di->setShared('guardian',
  function() use ($config) {
    $guardian = new Security\Guardian();

    return $guardian;
  }
);