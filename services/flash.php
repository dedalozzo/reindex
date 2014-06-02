<?php

/**
 * @file flash.php
 * @brief Creates the flash component.
 * @details
 * @author Filippo F. Fadda
 */


use Phalcon\Flash\Session as Flash;


// Returns the flash instance.
$di->setShared('flash', function() {
  return new Flash([
    'error' => 'alert alert-danger',
    'success' => 'alert alert-success',
    'notice' => 'alert alert-info',
  ]);
});