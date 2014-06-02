<?php

/**
 * @file crypt.php
 * @brief Creates a crypt component instance.
 * @details
 * @author Filippo F. Fadda
 */


$di->set('crypt', function() use ($config) {
  $crypt = new Phalcon\Crypt();
  $crypt->setKey($config->application->key);
  return $crypt;
});