<?php

//! @file session.php
//! @brief Starts the session the first time some component request the session service.
//! @details
//! @author Filippo F. Fadda


use Phalcon\Session\Adapter\Files as SessionAdapter;


// Creates a section adapter and returns it.
$di->setShared('session',
  function() {
    $session = new SessionAdapter();
    $session->start();
    return $session;
  }
);