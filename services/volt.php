<?php

//! @file volt.php
//! @brief Registers Volt as a service.
//! @details
//! @author Filippo F. Fadda


use Phalcon\Mvc\View\Engine\Volt;


// Creates an instance of Volt template engine and return it.
$di->setShared('volt',
  function($view, $di) use ($config) {
    $volt = new Volt($view, $di);

    $volt->setOptions(
      [
        'compiledPath' => __DIR__.$config->application->cacheDir.'volt/',
        'compiledExtension' => '.compiled',
        'compiledSeparator' => '_'
        //'compileAlways' => TRUE
      ]
    );

    return $volt;
  }
);