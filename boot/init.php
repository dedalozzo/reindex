<?php

//! @file init.php
//! @brief Initializes the application.
//! @details
//! @author Filippo F. Fadda


try {
  $config = new Phalcon\Config\Adapter\Ini(__DIR__.'/../pitpress/config.ini');

  // System bootstrap.
  include __DIR__."/../boot/loader.php";
  include __DIR__."/../boot/services.php";
  include __DIR__."/../boot/routes.php";

  // Creates the application object.
  $application = new \Phalcon\Mvc\Application();

  // Sets the dependency injector component.
  $application->setDI($di);

  // Handles the request.
  echo $application->handle()->getContent();
}
catch (Phalcon\Exception $e) {
  echo $e->getMessage();
}