<?php

//! @file url.php
//! @brief The Url component is used to generate all kind of URLs in the application.
//! @details
//! @author Filippo F. Fadda


use Phalcon\Mvc\Url as UrlResolver;


// Creates an instance of Url component and return it.
$di->setShared('url',
  function() use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
  }
);