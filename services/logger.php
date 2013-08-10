<?php

//! @file logger.php
//! @brief Just a trick to return the logger.
//! @details
//! @author Filippo F. Fadda


// Returns the $config object.
$di->setShared('logger',
  function() use ($logger) {
    return $logger;
  }
);