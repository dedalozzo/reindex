<?php

/**
 * @file assets.php
 * @brief Setting up the view.
 * @details
 * @author Filippo F. Fadda
 */


use Phalcon\Assets\Manager;


// Creates an instance of Assets Manager component and return it.
$di->setShared('assets',
  function() {
    return new Manager();
  }
);