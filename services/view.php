<?php

/**
 * @file view.php
 * @brief Setting up the view.
 * @details
 * @author Filippo F. Fadda
 */


use Phalcon\Mvc\View;


// Creates an instance of View component and return it.
$di->setShared('view',
  function() use ($root, $config) {
    $view = new View();
    $view->setViewsDir($root.'/'.$config->application->themesDir.'/'.$config->application->themeName."/volt");
    $view->registerEngines(['.volt' => 'volt']);
    return $view;
  }
);